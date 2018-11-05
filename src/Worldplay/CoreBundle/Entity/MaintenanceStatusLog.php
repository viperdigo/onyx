<?php

namespace Worldplay\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="maintenance_status_log")
 * @ORM\Entity()
 */
class MaintenanceStatusLog
{
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var MaintenanceProduct
     *
     * @ORM\ManyToOne(targetEntity="Worldplay\CoreBundle\Entity\MaintenanceProduct", inversedBy="statusLogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_maintenance", referencedColumnName="id")
     * })
     */
    private $maintenance;

    /**
     * @var string
     *
     * @ORM\Column(name="fk_user", type="integer", nullable=true)")
     */
    private $user;


    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false, columnDefinition="ENUM('entered', 'sent','received','returned','canceled')")
     */
    private $status;

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
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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

}