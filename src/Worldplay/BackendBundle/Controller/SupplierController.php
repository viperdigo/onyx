<?php

namespace Worldplay\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Worldplay\BackendBundle\Form\SupplierType;
use Worldplay\CoreBundle\Entity\Supplier;

/**
 * @Route("/supplier")
 */
class SupplierController extends Controller
{

    /**
     * @Route("/", name="supplier")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreBundle:Supplier')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/", name="supplier_create")
     * @Method("POST")
     * @Template("BackendBundle:Supplier:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Supplier();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', sprintf('Manutenção %s criada.', $entity->getName()));

            return $this->redirect($this->generateUrl('supplier'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param Supplier $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(Supplier $entity)
    {
        $form = $this->createForm(
            new SupplierType(),
            $entity,
            array(
                'action' => $this->generateUrl('supplier_create'),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'form-horizontal'
                )
            )
        );

        return $form;
    }

    /**
     * @Route("/new", name="supplier_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Supplier();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="supplier_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Supplier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @param Supplier $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(Supplier $entity)
    {
        $form = $this->createForm(
            new SupplierType(),
            $entity,
            array(
                'action' => $this->generateUrl('supplier_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'attr' => array(
                    'class' => 'form-horizontal'
                )
            )
        );

        return $form;
    }

    /**
     * @Route("/{id}", name="supplier_update")
     * @Method("PUT")
     * @Template("BackendBundle:Supplier:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Supplier')->find($id);

        if (!$entity instanceof Supplier) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', sprintf('Fornecedor %s alterado.', $entity->getName()));

            return $this->redirect($this->generateUrl('supplier'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Route("/{id}/delete", name="supplier_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Supplier')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supplier entity.');
        }

        try {

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Fornecedor excluído.');

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add('error', 'Não foi possível excluir, contate o administrador.');

        }

        return $this->redirect($this->generateUrl('supplier'));
    }

}
