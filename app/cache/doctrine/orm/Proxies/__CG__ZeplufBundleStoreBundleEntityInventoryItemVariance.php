<?php

namespace Proxies\__CG__\Zepluf\Bundle\StoreBundle\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class InventoryItemVariance extends \Zepluf\Bundle\StoreBundle\Entity\InventoryItemVariance implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setDate($date)
    {
        $this->__load();
        return parent::setDate($date);
    }

    public function getDate()
    {
        $this->__load();
        return parent::getDate();
    }

    public function setQuantity($quantity)
    {
        $this->__load();
        return parent::setQuantity($quantity);
    }

    public function getQuantity()
    {
        $this->__load();
        return parent::getQuantity();
    }

    public function setComment($comment)
    {
        $this->__load();
        return parent::setComment($comment);
    }

    public function getComment()
    {
        $this->__load();
        return parent::getComment();
    }

    public function setInventoryItem(\Zepluf\Bundle\StoreBundle\Entity\InventoryItem $inventoryItem = NULL)
    {
        $this->__load();
        return parent::setInventoryItem($inventoryItem);
    }

    public function getInventoryItem()
    {
        $this->__load();
        return parent::getInventoryItem();
    }

    public function setInventoryItemVarianceReason(\Zepluf\Bundle\StoreBundle\Entity\InventoryItemVarianceReason $inventoryItemVarianceReason = NULL)
    {
        $this->__load();
        return parent::setInventoryItemVarianceReason($inventoryItemVarianceReason);
    }

    public function getInventoryItemVarianceReason()
    {
        $this->__load();
        return parent::getInventoryItemVarianceReason();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'date', 'quantity', 'comment', 'inventoryItem', 'inventoryItemVarianceReason');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields as $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}