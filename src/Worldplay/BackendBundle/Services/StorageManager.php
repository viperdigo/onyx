<?php

namespace Worldplay\BackendBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Worldplay\CoreBundle\Entity\Customer;
use Worldplay\CoreBundle\Entity\Order;
use Worldplay\CoreBundle\Entity\Payment;
use Worldplay\CoreBundle\Entity\Product;

class StorageManager
{
    private $em;
    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container = null)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function takeStock(Product $product)
    {
        return true;
    }

    public function restocking(Product $product)
    {
        return true;
    }
}
