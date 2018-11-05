<?php

namespace Worldplay\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="customers")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Worldplay\CoreBundle\Repository\CustomerRepository")
 */
class Customer
{

    const STATUS_ACTIVATED = 'activated';
    const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(name="address", type="text", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(name="zipcode", type="string", length=8, nullable=true)
     */
    private $zipcode;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,  nullable=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2)
     */
    private $balance;

    /**
     * @var \Worldplay\CoreBundle\Entity\MaintenanceProduct
     *
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\MaintenanceProduct", mappedBy="customer")
     */
    private $maintenance;

    /**
     * @var datetime $createdAt
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var datetime $createdAt
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=true, columnDefinition="ENUM('activated', 'blocked')")
     */
    private $status;

    /**
     * @var CustomerStatusLog
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\CustomerStatusLog", mappedBy="customer")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $statusLogs;

    /**
     * @var Payment
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\Payment", mappedBy="customer")
     */
    private $payments;

    /**
     * @var Order
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\Order", mappedBy="customer")
     */
    private $orders;

    public function __construct()
    {
        $this->setStatus(Customer::STATUS_ACTIVATED);
    }

    /**
     * @return CustomerStatusLog
     */
    public function getStatusLogs()
    {
        return $this->statusLogs;
    }

    /**
     * @param CustomerStatusLog $statusLogs
     */
    public function setStatusLogs($statusLogs)
    {
        $this->statusLogs = $statusLogs;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param mixed $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance / 100;
    }

    /**
     * @return mixed
     */
    public function getMaintenance()
    {
        return $this->maintenance;
    }

    /**
     * @param mixed $maintenance
     */
    public function setMaintenance($maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return datetime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return Payment
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param Payment $payments
     */
    public function setPayments($payments)
    {
        $this->payments = $payments;
    }

    /**
     * @return Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param Order $orders
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

}
