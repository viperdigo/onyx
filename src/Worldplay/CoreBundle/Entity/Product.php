<?php

namespace Worldplay\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity
 */
class Product
{

    CONST TYPE_SIMPLE = 'simple';
    CONST TYPE_COMPOUND = 'compound';
    CONST TYPE_CABINET = 'cabinet';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(name="cost_value", type="decimal", precision=10, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    private $costValue;

    /**
     * @ORM\Column(name="sale_value", type="decimal", precision=10, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    private $saleValue;

    /**
     * @ORM\Column(name="storage", type="integer", nullable=true)
     */
    private $storage;

    /**
     * @ORM\Column(name="type", type="string", nullable=false, columnDefinition="ENUM('simple', 'cabinet', 'compound' )")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Worldplay\CoreBundle\Entity\Supplier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_supplier", referencedColumnName="id")
     * })
     */
    private $supplier;

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
     * @var \Worldplay\CoreBundle\Entity\MaintenanceProduct
     *
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\MaintenanceProduct", mappedBy="product")
     */
    private $maintenance;

    /**
     * @var \Worldplay\CoreBundle\Entity\StorageLog
     *
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\StorageLog", mappedBy="product")
     */
    private $storageLogs;

    /**
     * @var \Worldplay\CoreBundle\Entity\ProductComponent
     *
     * @ORM\OneToMany(targetEntity="Worldplay\CoreBundle\Entity\ProductComponent", mappedBy="product")
     */
    private $components;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCostValue()
    {
        return $this->costValue;
    }

    /**
     * @param mixed $costValue
     */
    public function setCostValue($costValue)
    {
        $this->costValue = $costValue / 100;
    }

    /**
     * @return mixed
     */
    public function getSaleValue()
    {
        return $this->saleValue;
    }

    /**
     * @param mixed $saleValue
     */
    public function setSaleValue($saleValue)
    {
        $this->saleValue = $saleValue/ 100;
    }

    /**
     * @return mixed
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param mixed $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param mixed $supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
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
     * @return MaintenanceProduct
     */
    public function getMaintenance()
    {
        return $this->maintenance;
    }

    /**
     * @param MaintenanceProduct $maintenance
     */
    public function setMaintenance($maintenance)
    {
        $this->maintenance = $maintenance;
    }

    /**
     * @return StorageLog
     */
    public function getStorageLogs()
    {
        return $this->storageLogs;
    }

    /**
     * @param StorageLog $storageLogs
     */
    public function setStorageLogs($storageLogs)
    {
        $this->storageLogs = $storageLogs;
    }

    /**
     * @return ProductComponents
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param ProductComponents $components
     */
    public function setComponents($components)
    {
        $this->components = $components;
    }

    public function __toString()
    {
        return $this->getDescription();
    }

}
