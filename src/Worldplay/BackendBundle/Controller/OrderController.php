<?php

namespace Worldplay\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Worldplay\CoreBundle\Entity\Customer;
use Worldplay\CoreBundle\Entity\Order;
use Worldplay\CoreBundle\Entity\OrderItem;
use Worldplay\CoreBundle\Entity\Product;

/**
 * @Route("/order")
 */
class OrderController extends Controller
{

    /**
     * @Route("/", name="order")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $filter = $this->get('filter')->createFilterBuilder('CoreBundle:Order')
            ->addField('id', array('length' => 2))
            ->addField('customer.id', array('length' => 1))
            ->addField('customer.name', array('length' => 4))
            ->addField('createdAt')
            ->addOrder('createdAt', 'DESC')
            ->addPagination(10)
            ->addCache(0)
            ->build();

        return array(
            'filter' => $filter,
            'result' => $filter->getResult(),
        );
    }

    /**
     * @Route("/new", name="order_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();

        $customers = $em->getRepository('CoreBundle:Customer')->findBy(array('status' => Customer::STATUS_ACTIVATED));
        $products = $em->getRepository('CoreBundle:Product')->findAll();

        return array(
            'customers' => $customers,
            'products' => $products,
        );
    }

    /**
     * @Route("/{id}/edit", name="order_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $customers = $em->getRepository('CoreBundle:Customer')->findBy(array('status' => Customer::STATUS_ACTIVATED));
        $products = $em->getRepository('CoreBundle:Product')->findAll();
        $order = $em->getRepository('CoreBundle:Order')->find($id);

        return array(
            'products' => $products,
            'customers' => $customers,
            'order' => $order,
        );
    }

    /**
     * @Route("/new", name="order_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $mnBalance = $this->get('worldplay.balance');

        try {
            $customer = $em->getRepository('CoreBundle:Customer')->find($request->get('customer'));

            if ($customer) {

                $note = $request->get('note');
                $products = $request->get('product');
                $quantities = $request->get('quantity');
                $soldValues = $request->get('soldValue');

                $order = new Order();
                $order->setCustomer($customer);
                $order->setNote($note);

                $key = 0;
                $subtotal = 0;
                $total = 0;
                foreach ($products as $p) {
                    $product = $em->getRepository('CoreBundle:Product')->find($p);

                    if ($product) {

                        if ($soldValues[$key] == 0) {
                            $saleValue = $product->getSaleValue();
                            $soldValue = $saleValue;
                            $subtotal = $saleValue * $quantities[$key];
                        } else {
                            $soldValue = $soldValues[$key];
                            $subtotal = $soldValues[$key] * $quantities[$key];
                        }

                        $orderItem = new OrderItem();
                        $orderItem->setProduct($product);
                        $orderItem->setQuantity($quantities[$key]);
                        $orderItem->setOrder($order);
                        $orderItem->setSoldValue($soldValue);
                        $orderItem->setSubtotal($subtotal);

                        $em->persist($orderItem);
                    }

                    $total += $subtotal;
                    $key++;
                }

                $mnBalance->debitBalance($customer, $total);

                $order->setAmount($total);
                $em->persist($order);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans(
                        'Order %number% created',
                        array("%number%" => $order->getId())
                    )
                );

            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    $this->get('translator')->trans('Customer not selected')
                );
            }

        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf('Code: %s Message: %s', $e->getCode(), $e->getMessage())
            );
        }

        return $this->redirect($this->generateUrl('order'));
    }

    /**
     * @Route("/edit", name="order_update")
     * @Method("POST")
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mnBalance = $this->get('worldplay.balance');

        try {

            $note = $request->get('note');
            $products = $request->get('product');
            $quantities = $request->get('quantity');
            $soldValues = $request->get('soldValue');

            $orderId = $request->get('id');

            //Remove Items
            $orderItems = $em->getRepository('CoreBundle:OrderItem')->findBy(array('order' => $orderId));
            foreach ($orderItems as $item) {
                $em->remove($item);
            }

            $order = $em->getRepository('CoreBundle:Order')->findOneBy(array('id' => $orderId));
            $order->setNote($note);
            $customer = $order->getCustomer();

            $key = 0;
            $subtotal = 0;
            $total = 0;
            foreach ($products as $p) {

                $product = $em->getRepository('CoreBundle:Product')->find($p);

                if ($product) {

                    if ($soldValues[$key] == 0) {
                        $saleValue = $product->getSaleValue();
                        $soldValue = $saleValue;
                        $subtotal = $saleValue * $quantities[$key];
                    } else {
                        $soldValue = $soldValues[$key];
                        $subtotal = $soldValues[$key] * $quantities[$key];
                    }

                    $orderItem = new OrderItem();
                    $orderItem->setProduct($product);
                    $orderItem->setQuantity($quantities[$key]);
                    $orderItem->setOrder($order);
                    $orderItem->setSoldValue($soldValue);
                    $orderItem->setSubtotal($subtotal);

                    $em->persist($orderItem);
                }

                $total += $subtotal;
                $key++;
            }

            $oldAmount = $order->getAmount();

            $mnBalance->debitBalance($customer, $total - $oldAmount);

            $order->setAmount($total);
            $em->persist($order);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans(
                    'Order %number% updated',
                    array("%number%" => $order->getId())
                )
            );

        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf('Code: %s Message: %s', $e->getCode(), $e->getMessage())
            );
        }

        return $this->redirect($this->generateUrl('order'));

    }

    /**
     * @Route("/{id}/delete", name="order_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $mnBalance = $this->get('worldplay.balance');

        $entity = $em->getRepository('CoreBundle:Order')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Order entity.');
        }

        try {

            $mnBalance->creditBalance($entity->getCustomer(), $entity->getAmount());
            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('Order %number% excluded.', array('%number%' => $entity->getId()))
            );

        } catch (\Exception $e) {

            $this->get('session')->getFlashBag()->add(
                'error',
                $this->get('translator')->trans('Could not perform this action, contact your administrator')
            );

        }

        return $this->redirect($this->generateUrl('order'));
    }
}
