<?php

namespace Proxies\__CG__\Zepluf\Bundle\StoreBundle\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class InvoiceStatus extends \Zepluf\Bundle\StoreBundle\Entity\InvoiceStatus implements \Doctrine\ORM\Proxy\Proxy
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

    public function setEntryDate($entryDate)
    {
        $this->__load();
        return parent::setEntryDate($entryDate);
    }

    public function getEntryDate()
    {
        $this->__load();
        return parent::getEntryDate();
    }

    public function setInvoice(\Zepluf\Bundle\StoreBundle\Entity\Invoice $invoice = NULL)
    {
        $this->__load();
        return parent::setInvoice($invoice);
    }

    public function getInvoice()
    {
        $this->__load();
        return parent::getInvoice();
    }

    public function setInvoiceStatusType(\Zepluf\Bundle\StoreBundle\Entity\InvoiceStatusType $invoiceStatusType = NULL)
    {
        $this->__load();
        return parent::setInvoiceStatusType($invoiceStatusType);
    }

    public function getInvoiceStatusType()
    {
        $this->__load();
        return parent::getInvoiceStatusType();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'entryDate', 'invoice', 'invoiceStatusType');
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