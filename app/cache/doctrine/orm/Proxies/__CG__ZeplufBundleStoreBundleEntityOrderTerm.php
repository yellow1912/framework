<?php

namespace Proxies\__CG__\Zepluf\Bundle\StoreBundle\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class OrderTerm extends \Zepluf\Bundle\StoreBundle\Entity\OrderTerm implements \Doctrine\ORM\Proxy\Proxy
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

    public function setValue($value)
    {
        $this->__load();
        return parent::setValue($value);
    }

    public function getValue()
    {
        $this->__load();
        return parent::getValue();
    }

    public function setOrder(\Zepluf\Bundle\StoreBundle\Entity\Order $order = NULL)
    {
        $this->__load();
        return parent::setOrder($order);
    }

    public function getOrder()
    {
        $this->__load();
        return parent::getOrder();
    }

    public function setOrderItem(\Zepluf\Bundle\StoreBundle\Entity\OrderItem $orderItem = NULL)
    {
        $this->__load();
        return parent::setOrderItem($orderItem);
    }

    public function getOrderItem()
    {
        $this->__load();
        return parent::getOrderItem();
    }

    public function setTermType(\Zepluf\Bundle\StoreBundle\Entity\TermType $termType = NULL)
    {
        $this->__load();
        return parent::setTermType($termType);
    }

    public function getTermType()
    {
        $this->__load();
        return parent::getTermType();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'value', 'order', 'orderItem', 'termType');
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