<?php

namespace Worldplay\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="maintenance_products")
 * @ORM\Entity
 */
class MaintenanceProduct
{

    CONST STATUS_ENTERED = 'entered';
    CONST STATUS_SENT = 'sent';
    CONST STATUS_RECEIVED = 'received';
    CONST STATUS_RETURNED = 'returned';
    CONST STATUS_CANCELED = 'canceled';

    private $statuses = array(
        self::STATUS_ENTERED,
        self::STATUS_SENT,
        self::STATUS_RECEIVED,
        self::STATUS_RETURNED
    );

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="Worldplay\CoreBundle\Entity\Customer", inversedBy="maintenance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_customer", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank()
     */
    private $customer;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Worldplay\CoreBundle\Entity\Product", inversedBy="maintenance")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_product", referencedColumnName="id", nullable=false)
     * })
     * @Assert\NotBlank()
     */
    private $product;

    /**
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false, columnDefinition="ENUM('entered', 'sent','received','returned','canceled')")
     */
    private $status;

    /**
     * @ORM\Column(name="note", type="text", length=255, nullable=true)
     */
    private $note;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var MaintenanceStatusLog
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\MaintenanceStatusLog", mappedBy="maintenance")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $statusLogs;

    public function __construct()
    {
        $this->setStatus(MaintenanceProduct::STATUS_ENTERED);
    }

    /**
     * @return mixed
     */
    public function getNextStatus()
    {
        $nextStatus = current(array_slice($this->statuses, array_search($this->getStatus(), array_values($this->statuses)) + 1, 1));

        return $nextStatus;
    }

    /**
     * @return MaintenanceStatusLog
     */
    public function getStatusLogs()
    {
        return $this->statusLogs;
    }

    /**
     * @param MaintenanceStatusLog $statusLogs
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
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

}
