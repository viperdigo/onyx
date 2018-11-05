<?php

namespace Worldplay\BackendBundle\Controller;

use Filter\FilterBundle\Action\CsvExportAction;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Worldplay\BackendBundle\Form\MaintenanceType;
use Worldplay\CoreBundle\Entity\MaintenanceProduct;

/**
 * @Route("/maintenance")
 */
class MaintenanceController extends Controller
{

    /**
     * @Route("/", name="maintenance")
     * @Route("/{id}/print", name="maintenance_print")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($id = false)
    {

        $maintenancePrint = false;

        if($id)
        {
            $em = $this->getDoctrine()->getManager();
            $maintenancePrint = $em->getRepository('CoreBundle:MaintenanceProduct')->findOneBy(array('id'=>$id));
        }

        $filter = $this->get('filter')->createFilterBuilder('CoreBundle:MaintenanceProduct')
            ->addField('id',array('length'=>1))
            ->addField('customer.id',array('length'=>1))
            ->addField('customer.name',array('length'=>3))
            ->addField('product.description',array('length'=>3))
            ->addField('statusLogs.status',array('length'=>3))
            ->addOrder('statusLogs.createdAt', 'DESC')
            ->addPagination(10)
            ->addCache(0)
            ->build();

        return array(
            'filter' => $filter,
            'result' => $filter->getResult(),
            'maintenancePrint' => $maintenancePrint
        );
    }

    /**
     * @Route("/", name="maintenance_create")
     * @Method("POST")
     * @Template("BackendBundle:Maintenance:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new MaintenanceProduct();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Manutenção %s do Cliente %s e Produto .', $entity->getId(), $entity->getCustomer()->getName())
            );

            return $this->redirect($this->generateUrl('maintenance_print',array('id'=> $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param MaintenanceProduct $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(MaintenanceProduct $entity)
    {
        $form = $this->createForm(
            new MaintenanceType(),
            $entity,
            array(
                'action' => $this->generateUrl('maintenance_create'),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/new", name="maintenance_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new MaintenanceProduct();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/edit", name="maintenance_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:MaintenanceProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Maintenance entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @param MaintenanceProduct $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createEditForm(MaintenanceProduct $entity)
    {
        $form = $this->createForm(
            new MaintenanceType(),
            $entity,
            array(
                'action' => $this->generateUrl('maintenance_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/{id}", name="maintenance_update")
     * @Method("PUT")
     * @Template("BackendBundle:Maintenance:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:MaintenanceProduct')->find($id);

        if (!$entity instanceof MaintenanceProduct) {
            throw $this->createNotFoundException('Unable to find Maintenance entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Manutenção %s alterado.', $entity->getName())
            );

            return $this->redirect($this->generateUrl('maintenance'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Route("/{id}/delete", name="maintenance_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:MaintenanceProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Maintenance entity.');
        }

        try {

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Manutenção excluída.');

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add('error', 'Não foi possível excluir, contate o administrador.');

        }

        return $this->redirect($this->generateUrl('maintenance'));
    }

    /**
     * @Route("/{id}/change_status", name="maintenance_change_status")
     * @Method("GET")
     * @Template()
     */
    public function changeStatusAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:MaintenanceProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Maintenance entity.');
        }

        try {

            $statuses = array(
                MaintenanceProduct::STATUS_ENTERED,
                MaintenanceProduct::STATUS_SENT,
                MaintenanceProduct::STATUS_RECEIVED,
                MaintenanceProduct::STATUS_RETURNED,
            );

            $nextStatus = current(
                array_slice($statuses, array_search($entity->getStatus(), array_values($statuses)) + 1, 1)
            );

            if ($nextStatus) {
                $entity->setStatus($nextStatus);
                $em->persist($entity);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    sprintf(
                        "Status da manutenção: %s, Cliente: %s alterado para: %s",
                        $entity->getId(),
                        $entity->getCustomer()->getName(),
                        $entity->getStatus()
                    )
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    sprintf(
                        "Status da manutenção: %s, Cliente: %s não foi alterado",
                        $entity->getId(),
                        $entity->getCustomer()->getName()
                    )
                );
            }


        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'error',
                'Não foi possível alterar o status, contate o administrador.'
            );

        }

        return $this->redirect($this->generateUrl('maintenance'));
    }

    /**
     * @Route("/{id}/cancel", name="maintenance_cancel")
     * @Method("GET")
     * @Template()
     */
    public function cancelAction($id)
    {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:MaintenanceProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Maintenance entity.');
        }

        try {

            $entity->setStatus(MaintenanceProduct::STATUS_CANCELED);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(
                    "Status da manutenção: %s, cancelada.",
                    $entity->getId()
                )
            );

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'error',
                'Não foi possível cancelar'
            );

        }

        return $this->redirect($this->generateUrl('maintenance'));
    }

}
