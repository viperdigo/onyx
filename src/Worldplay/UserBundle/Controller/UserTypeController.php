<?php

namespace Worldplay\UserBundle\Controller;

use Worldplay\CoreBundle\Entity\UserType;
use Worldplay\UserBundle\Form\UserTypeType;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;



/**
 * User controller.
 *
 * @Route("/user_type")
 */
class UserTypeController extends Controller
{

    /**
     * @Route("/", name="user_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CoreBundle:UserType')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/new", name="user_type_create")
     * @Method("POST")
     * @Template("UserBundle:UserType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new UserType();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_type'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param UserType $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UserType $entity)
    {

        $form = $this->createForm(
            new UserTypeType(),
            $entity,
            array(
                'action' => $this->generateUrl('user_type_create'),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'form-horizontal'
                )
            )
        );


        return $form;
    }

    /**
     * @Route("/new", name="user_type_new")
     * @Method("GET")
     * @Template("UserBundle:UserType:new.html.twig")
     */
    public function newAction()
    {
        $entity = new UserType();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:UserType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserType entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(UserType $entity)
    {
        $form = $this->createForm(
            new UserTypeType(),
            $entity,
            array(
                'action' => $this->generateUrl('user_type_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'attr' => array(
                    'class' => 'form-horizontal'
                )
            )
        );

        return $form;
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_type_update")
     * @Method("PUT")
     * @Template("UserBundle:UserType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:UserType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserType entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        try {

            if ($editForm->isValid()) {

                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Atualizado com sucesso!'
                );

                return $this->redirect($this->generateUrl('user_type'));
            }
        } catch (Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'warning',
                'Não foi possível atualizar, contate o administrador.'
            );

            return array(
                'entity' => $entity,
                'edit_form' => $editForm->createView(),
            );

        }

    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="user_type_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:UserType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserType entity.');
        }

        try {

            $em = $this->getDoctrine()->getManager();

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Tipo excluído com sucesso!'
            );

        } catch (Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'warning',
                'Não foi possível excluir o tipo.'
            );

        }

        return $this->redirect($this->generateUrl('user_type'));

    }

}
