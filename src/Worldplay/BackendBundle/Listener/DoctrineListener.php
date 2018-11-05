<?php
namespace Worldplay\BackendBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Worldplay\CoreBundle\Entity\AuditLog;
use Worldplay\CoreBundle\Entity\Customer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Worldplay\CoreBundle\Entity\CustomerBalanceLog;
use Worldplay\CoreBundle\Entity\CustomerStatusLog;
use Worldplay\CoreBundle\Entity\MaintenanceProduct;
use Worldplay\CoreBundle\Entity\MaintenanceStatusLog;
use Worldplay\CoreBundle\Entity\Payment;
use Worldplay\CoreBundle\Entity\Product;
use Worldplay\CoreBundle\Entity\StorageLog;
use Worldplay\CoreBundle\Entity\User;

class DoctrineListener
{
    private $container;
    private $needsFlush;

    private $logClasses = array(
        'CustomerStatusLog',
        'MaintenanceStatusLog',
        'CustomerBalanceLog',
        'StorageLog'
    );
    private $skipMaintenanceLog = false;
    private $skipCustomerLog = false;
    private $skipBalanceLog = false;
    private $skipStorageLog = false;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    private function auditLog($args, $changes = '')
    {

        if (!$args instanceof preUpdateEventArgs && !$args instanceof LifecycleEventArgs) {
            return;
        }

        $entity = $args->getEntity();
        $c = explode('\\', get_class($entity));
        $v = array_shift($c);
        $c = array_pop($c);

        if ($v != 'Worldplay' || in_array($c, $this->logClasses)) {
            return;
        }

        if (!$this->container->get('security.token_storage')->getToken()) {
            return;
        }

        $changesArray = array($changes);
        if ($changes == 'update') {
            $changes = $args->getEntityChangeSet();

            if (is_array($changes)) {
                foreach ($changes as $field => $change) {
                    if (!isset($change[1])) {
                        continue;
                    }
                    $x = $change[1] instanceof \DateTime ? $change[1]->format('Y-m-d H:i') : $change[1];
                    $changesArray[] = "{$field}:{$x}";
                }
            }
        }

        $entity = $args->getEntity();
        $userIp = $this->container->get('request')->getClientIp();
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if ($user instanceof User && is_object($entity)) {
            $auditLog = new AuditLog();
            $auditLog->setUser($user);
            $auditLog->setEntity($c);
            $auditLog->setEntityId($entity->getId());
            $auditLog->setValues(implode(' ', $changesArray));
            $auditLog->setUserIp($userIp);
            $args->getEntityManager()->persist($auditLog);
            $this->needsFlush = true;
        }
    }

    private function statusLog($args)
    {

        if (!$args instanceof preUpdateEventArgs && !$args instanceof LifecycleEventArgs) {
            return;
        }

        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Customer && !$this->skipCustomerLog) {

            $user = null;
            $token = $this->container->get('security.token_storage')->getToken();

            if ($token) {
                $user = $token->getUser();

                if ($user instanceof User) {
                    $user = $user->getId();
                } else {
                    $user = null;
                }
            }

            $log = new CustomerStatusLog();
            $log->setStatus($entity->getStatus());
            $log->setCustomer($entity);
            $log->setUser($user);
            $em->persist($log);

            $this->needsFlush = true;

            return;
        }

        if ($entity instanceof MaintenanceProduct && !$this->skipMaintenanceLog) {

            $user = null;
            $token = $this->container->get('security.token_storage')->getToken();

            if ($token) {
                $user = $token->getUser();

                if ($user instanceof User) {
                    $user = $user->getId();
                } else {
                    $user = null;
                }
            }

            $log = new MaintenanceStatusLog();

            $log->setStatus($entity->getStatus());
            $log->setMaintenance($entity);
            $log->setUser($user);
            $em->persist($log);

            $this->needsFlush = true;

            return;
        }

    }

    private function balanceLog($args)
    {
        if (!$args instanceof preUpdateEventArgs && !$args instanceof LifecycleEventArgs) {
            return;
        }

        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Customer && !$this->skipBalanceLog) {

            $user = null;
            $token = $this->container->get('security.token_storage')->getToken();

            if ($token) {
                $user = $token->getUser();

                if (!$user instanceof User) {
                    $user = null;
                }
            }

            $route = $this->container->get('router')->match($this->container->get('request')->getPathInfo());

            $log = new CustomerBalanceLog();
            $log->setBalance($entity->getBalance());
            $log->setCustomer($entity);
            $log->setController($route['_controller']);
            $log->setUser($user);
            $em->persist($log);

            $this->needsFlush = true;

            return;
        }

    }

    private function storageLog($args)
    {
        if (!$args instanceof preUpdateEventArgs && !$args instanceof LifecycleEventArgs) {
            return;
        }

        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Product && !$this->skipStorageLog) {

            $user = null;
            $token = $this->container->get('security.token_storage')->getToken();

            if ($token) {
                $user = $token->getUser();

                if (!$user instanceof User) {
                    $user = null;
                }
            }

            $route = $this->container->get('router')->match($this->container->get('request')->getPathInfo());
            $log = new StorageLog();

            $log->setAmount($entity->getStorage());
            $log->setProduct($entity);
            $log->setController($route['_controller']);
            $log->setUser($user);
            $em->persist($log);

            $this->needsFlush = true;

            return;
        }

    }

    public function preUpdate(preUpdateEventArgs $args)
    {
        self::auditLog($args, 'update');

        $entity = $args->getEntity();

        if ($entity instanceof Customer && !$args->hasChangedField('status')) {
            $this->skipCustomerLog = true;

            return;
        }

        if ($entity instanceof MaintenanceProduct && !$args->hasChangedField('status')) {
            $this->skipMaintenanceLog = true;

            return;
        }

        if ($entity instanceof Customer && !$args->hasChangedField('balance')) {
            $this->skipBalanceLog = true;

            return;
        }

        if ($entity instanceof Product && !$args->hasChangedField('storage')) {
            $this->skipStorageLog = true;

            return;
        }

    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        self::statusLog($args);
        self::storageLog($args);
        self::balanceLog($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
//        self::auditLog($args, 'insert');
        self::statusLog($args);
        self::storageLog($args);
        self::balanceLog($args);

    }

    public function preRemove(LifecycleEventArgs $args)
    {
        self::auditLog($args, 'remove');

    }

    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        if ($this->needsFlush) {
            $this->needsFlush = false;
            $this->skipCustomerLog = false;
            $this->skipMaintenanceLog = false;
            $this->skipBalanceLog = false;
            $this->skipStorageLog = false;
            $eventArgs->getEntityManager()->flush();
        }
    }

}