<?php

namespace Worldplay\BackendBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Worldplay\CoreBundle\Entity\Customer;
use Worldplay\CoreBundle\Entity\Order;
use Worldplay\CoreBundle\Entity\Payment;

class BalanceManager
{
    private $em;
    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container = null)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function creditBalance(Customer $customer, $amount)
    {
        $currentBalance = $customer->getBalance();
        $creditBalance = $currentBalance + $amount;

        $customer->setBalance($creditBalance);

        $this->container->get('session')->getFlashBag()->add(
            'info',
            $this->container->get('translator')->trans('Customer %name% balance updated.', array('%name%' => $customer->getName()))
        );

        return true;
    }

    public function debitBalance(Customer $customer, $amount)
    {
        $currentBalance = $customer->getBalance();
        $creditBalance = $currentBalance - $amount;

        $customer->setBalance($creditBalance);

        $this->container->get('session')->getFlashBag()->add(
            'info',
            $this->container->get('translator')->trans('Customer %name% balance updated.', array('%name%' => $customer->getName()))
        );

        return true;
    }

}
