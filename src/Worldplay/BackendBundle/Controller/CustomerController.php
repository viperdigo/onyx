<?php

namespace Worldplay\BackendBundle\Controller;

use Filter\FilterBundle\Action\CsvExportAction;
use Filter\FilterBundle\Action\PdfExportAction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Translatable\Fixture\Type\Custom;
use Worldplay\BackendBundle\Filter\Action\CustomerBalanceAction;
use Worldplay\BackendBundle\Filter\Action\CustomerBalanceExportPdfAction;
use Worldplay\CoreBundle\Entity\Customer;
use Worldplay\BackendBundle\Form\CustomerType;
use Worldplay\CoreBundle\Entity\Order;
use Worldplay\CoreBundle\Entity\Payment;
use Worldplay\CoreBundle\Entity\Product;

/**
 * Customer controller.
 *
 * @Route("/customer")
 */
class CustomerController extends Controller
{

    /**
     * @Route("/", name="customer")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {

        $customerName = $request->get('name');

        $filter = $this->get('filter')->createFilterBuilder('CoreBundle:Customer')
            ->addField('id', array('length' => '1'))
            ->addField('name', array('value' => $customerName, 'length' => '5'))
            ->addField('status', array('length' => '4'))
            ->addPagination(10)
            ->addAction(
                array('name' => 'customer_export_pdf', 'icon' => 'fa-file-pdf-o'),
                new CustomerBalanceExportPdfAction(
                    $this->get('knp_snappy.pdf'), $this->get('twig'),
                    'BackendBundle:Report:customer.html.twig'
                )
            )
            ->addResultAction(new CustomerBalanceAction())
            ->addCache(0)
            ->build();


        return array(
            'filter' => $filter,
        );
    }

    /**
     * @Route("/{id}/show", name="customer_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $customer = $em->getRepository('CoreBundle:Customer')->find($id);


        return array(
            'customer' => $customer,
        );
    }

    /**
     * @Route("/statement/{id}", name="customer_statement")
     * @Method("GET")
     * @Template()
     */
    public function statementAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $customer = $em->getRepository('CoreBundle:Customer')->findOneBy(array('id' => $id));
        $payments = $em->getRepository('CoreBundle:Payment')->findBy(array('customer' => $id));
        $orders = $em->getRepository('CoreBundle:Order')->findBy(array('customer' => $id));

        $tabs = array(10, 20, 30, 'All');

        $statement['tab1'] = $em->getRepository('CoreBundle:Customer')->getStatement($id, $tabs[0]);
        $statement['tab2'] = $em->getRepository('CoreBundle:Customer')->getStatement($id, $tabs[1]);
        $statement['tab3'] = $em->getRepository('CoreBundle:Customer')->getStatement($id, $tabs[2]);
        $statement['tab4'] = $em->getRepository('CoreBundle:Customer')->getStatement($id);

        $totalCredit = 0;
        foreach ($payments as $payment) {
            if (!$payment instanceof Payment) {
                continue;
            }

            $totalCredit += $payment->getAmount();
        }

        $totalDebit = 0;
        foreach ($orders as $order) {
            if (!$order instanceof Order) {
                continue;
            }

            $totalDebit += $order->getAmount();
        }

        $statement['total']['credit'] = $totalCredit;
        $statement['total']['debit'] = $totalDebit;

        return array(
            'tabs' => $tabs,
            'customer' => $customer,
            'statement' => $statement,
        );

    }

    /**
     * @Route("/statement/{id}/{days}", name="customer_statement_export")
     * @Route("/statement/", name="customer_statement_exp")
     * @Method("GET")
     * @Template()
     */
    public function exportPdfAction($id, $days)
    {
        $em = $this->getDoctrine()->getManager();

        if (!is_numeric($days)) {
            $days = false;
        }

        $customer = $em->getRepository('CoreBundle:Customer')->findOneBy(array('id' => $id));

        $payments = $customer->getPayments();
        $sumPayments = 0;
        foreach ($payments as $payment) {
            if (!$payment instanceof Payment) {
                continue;
            }

            $sumPayments += $payment->getAmount();
        }

        $orders = $customer->getOrders();
        $sumOrders = 0;
        foreach ($orders as $order) {
            if (!$order instanceof Order) {
                continue;
            }
            $sumOrders += $order->getAmount();
        }

        $data = $em->getRepository('CoreBundle:Customer')->getStatement($id, $days);

        $html = $this->renderView(
            'BackendBundle:Report:statement.html.twig',
            array(
                'customer' => $customer,
                'statement' => $data,
                'sumPayments' => $sumPayments,
                'sumOrders' => $sumOrders,
            )
        );

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
            )
        );

    }

    /**
     * @Route("/new", name="customer_create")
     * @Method("POST")
     * @Template("BackendBundle:Customer:new.html.twig")
     */
    public function createAction(Request $request)
    {

        $entity = new Customer();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Customer %name% created.', array('%name%' => $entity->getName()))
            );

            return $this->redirect($this->generateUrl('customer'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param Customer $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Customer $entity)
    {
        $form = $this->createForm(
            new CustomerType(),
            $entity,
            array(
                'action' => $this->generateUrl('customer_create'),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/new", name="customer_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Customer();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="customer_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @param Customer $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Customer $entity)
    {
        $form = $this->createForm(
            new CustomerType(),
            $entity,
            array(
                'action' => $this->generateUrl('customer_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

//        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/{id}", name="customer_update")
     * @Method("PUT")
     * @Template("BackendBundle:Customer:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Customer')->find($id);

        if (!$entity instanceof Customer) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Customer %name% changed.', array("%name%" => $entity->getName()))
            );

            return $this->redirect($this->generateUrl('customer'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Route("/{id}/delete", name="customer_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        try {

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Customer %name% excluded.', array('%name%' => $entity->getName()))
            );

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('Could not perform this action, contact your administrator')
            );

        }

        return $this->redirect($this->generateUrl('customer'));
    }

    /**
     * @Route("/{id}/block", name="customer_block")
     * @Method("GET")
     * @Template()
     */
    public function blockAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        try {

            if ($entity->getStatus() == Customer::STATUS_ACTIVATED) {
                $entity->setStatus(Customer::STATUS_BLOCKED);
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('Customer %name% blocked.', array('%name%' => $entity->getName()))
                );

                $em->persist($entity);
                $em->flush();
            } else {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    $this->get('translator')->trans('Customer is already locked.')
                );
            }

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('Could not perform this action, contact your administrator')
            );

        }

        return $this->redirect($this->generateUrl('customer'));


    }

    /**
     * @Route("/{id}/activate", name="customer_activate")
     * @Method("GET")
     * @Template()
     */
    public function activateAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Customer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customer entity.');
        }

        try {

            if ($entity->getStatus() == Customer::STATUS_BLOCKED) {
                $entity->setStatus(Customer::STATUS_ACTIVATED);
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('Customer %name% activated.', array("%name%" => $entity->getName()))
                );

                $em->persist($entity);
                $em->flush();
            } else {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    $this->get('translator')->trans(
                        'Could not enable the customer %name%',
                        array("%name%" => $entity->getName())
                    )
                );
            }

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('Could not perform this action, contact your administrator')
            );

        }

        return $this->redirect($this->generateUrl('customer'));

    }

}
