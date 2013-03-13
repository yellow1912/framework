<?php

namespace Zepluf\Bundle\StoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Carrier
 *
 * @ORM\Table(name="carrier")
 * @ORM\Entity
 */
class Carrier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="ShipmentMethodType", mappedBy="carrier")
     */
    private $shipmentMethodType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shipmentMethodType = new \Doctrine\Common\Collections\ArrayCollection();
    }
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Carrier
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add shipmentMethodType
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ShipmentMethodType $shipmentMethodType
     * @return Carrier
     */
    public function addShipmentMethodType(\Zepluf\Bundle\StoreBundle\Entity\ShipmentMethodType $shipmentMethodType)
    {
        $this->shipmentMethodType[] = $shipmentMethodType;
    
        return $this;
    }

    /**
     * Remove shipmentMethodType
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\ShipmentMethodType $shipmentMethodType
     */
    public function removeShipmentMethodType(\Zepluf\Bundle\StoreBundle\Entity\ShipmentMethodType $shipmentMethodType)
    {
        $this->shipmentMethodType->removeElement($shipmentMethodType);
    }

    /**
     * Get shipmentMethodType
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getShipmentMethodType()
    {
        return $this->shipmentMethodType;
    }
}