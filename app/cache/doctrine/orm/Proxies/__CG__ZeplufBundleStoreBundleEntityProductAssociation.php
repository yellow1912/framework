<?php

namespace Proxies\__CG__\Zepluf\Bundle\StoreBundle\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class ProductAssociation extends \Zepluf\Bundle\StoreBundle\Entity\ProductAssociation implements \Doctrine\ORM\Proxy\Proxy
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

    public function setFromDate($fromDate)
    {
        $this->__load();
        return parent::setFromDate($fromDate);
    }

    public function getFromDate()
    {
        $this->__load();
        return parent::getFromDate();
    }

    public function setThroughDate($throughDate)
    {
        $this->__load();
        return parent::setThroughDate($throughDate);
    }

    public function getThroughDate()
    {
        $this->__load();
        return parent::getThroughDate();
    }

    public function setReason($reason)
    {
        $this->__load();
        return parent::setReason($reason);
    }

    public function getReason()
    {
        $this->__load();
        return parent::getReason();
    }

    public function setFromProduct(\Zepluf\Bundle\StoreBundle\Entity\Product $fromProduct = NULL)
    {
        $this->__load();
        return parent::setFromProduct($fromProduct);
    }

    public function getFromProduct()
    {
        $this->__load();
        return parent::getFromProduct();
    }

    public function setToProduct(\Zepluf\Bundle\StoreBundle\Entity\Product $toProduct = NULL)
    {
        $this->__load();
        return parent::setToProduct($toProduct);
    }

    public function getToProduct()
    {
        $this->__load();
        return parent::getToProduct();
    }

    public function setProductAssociationType(\Zepluf\Bundle\StoreBundle\Entity\ProductAssociationType $productAssociationType = NULL)
    {
        $this->__load();
        return parent::setProductAssociationType($productAssociationType);
    }

    public function getProductAssociationType()
    {
        $this->__load();
        return parent::getProductAssociationType();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'fromDate', 'throughDate', 'reason', 'fromProduct', 'toProduct', 'productAssociationType');
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