<?php

namespace Worldplay\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Worldplay\BackendBundle\Form\PaymentType;
use Worldplay\CoreBundle\Entity\Payment;

/**
 * @Route("/payment")
 */
class PaymentController extends Controller
{

    /**
     * @Route("/", name="payment")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $filter = $this->get('filter')->createFilterBuilder('CoreBundle:Payment')
            ->addField('id')
            ->addField('customer.id')
            ->addField('customer.name',array('length'=>3))
            ->addField('createdAt')
            ->addOrder('receivedAt', 'DESC')
            ->addPagination(10)
            ->addCache(0)
            ->build();

        return array(
            'filter' => $filter,
            'result' => $filter->getResult(),
        );
    }

    /**
     * @Route("/new", name="payment_create")
     * @Method("POST")
     * @Template("BackendBundle:Payment:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Payment();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $mnBalance = $this->get('worldplay.balance');

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity->setAmount($entity->getAmount());
            $em->persist($entity);

            $mnBalance->creditBalance($entity->getCustomer(), $entity->getAmount());

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Pagamento %s recebido.', $entity->getId())
            );

            $em->flush();

            return $this->redirect($this->generateUrl('payment'));

        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param Payment $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Payment $entity)
    {
        $form = $this->createForm(
            new PaymentType(),
            $entity,
            array(
                'action' => $this->generateUrl('payment_create'),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/new", name="payment_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Payment();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="payment_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Payment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Payment entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @param Payment $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Payment $entity)
    {
        $form = $this->createForm(
            new PaymentType(),
            $entity,
            array(
                'action' => $this->generateUrl('payment_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/{id}", name="payment_update")
     * @Method("PUT")
     * @Template("BackendBundle:Payment:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $mnBalance = $this->get('worldplay.balance');

        $entity = $em->getRepository('CoreBundle:Payment')->find($id);
        $paymentAmount = $entity->getAmount();

        if (!$entity instanceof Payment) {
            throw $this->createNotFoundException('Unable to find Payment entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            $customer = $entity->getCustomer();
            $editedAmount = ($entity->getAmount());
            $calculatedAmount = $editedAmount - $paymentAmount;
            $entity->setAmount($editedAmount);

            $mnBalance->creditBalance($customer, $calculatedAmount);

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Pagamento %s alterado.', $customer->getName())
            );

            return $this->redirect($this->generateUrl('payment'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Route("/{id}/delete", name="payment_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $mnBalance = $this->get('worldplay.balance');

        $entity = $em->getRepository('CoreBundle:Payment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Payment entity.');
        }

        try {

            $customer = $entity->getCustomer();
            $amountDeleted = $entity->getAmount();
            $mnBalance->debitBalance($customer, $amountDeleted);
            $em->remove($entity);


            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Fornecedor excluído.');

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add('error', 'Não foi possível excluir, contate o administrador.');

        }

        return $this->redirect($this->generateUrl('payment'));
    }

}
