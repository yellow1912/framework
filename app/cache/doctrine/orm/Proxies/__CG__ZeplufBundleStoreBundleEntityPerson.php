<?php

namespace Proxies\__CG__\Zepluf\Bundle\StoreBundle\Entity;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class Person extends \Zepluf\Bundle\StoreBundle\Entity\Person implements \Doctrine\ORM\Proxy\Proxy
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

    public function setCurrentLastName($currentLastName)
    {
        $this->__load();
        return parent::setCurrentLastName($currentLastName);
    }

    public function getCurrentLastName()
    {
        $this->__load();
        return parent::getCurrentLastName();
    }

    public function setCurrentFirstName($currentFirstName)
    {
        $this->__load();
        return parent::setCurrentFirstName($currentFirstName);
    }

    public function getCurrentFirstName()
    {
        $this->__load();
        return parent::getCurrentFirstName();
    }

    public function setCurrentMiddleName($currentMiddleName)
    {
        $this->__load();
        return parent::setCurrentMiddleName($currentMiddleName);
    }

    public function getCurrentMiddleName()
    {
        $this->__load();
        return parent::getCurrentMiddleName();
    }

    public function setCurrentPersonalTitle($currentPersonalTitle)
    {
        $this->__load();
        return parent::setCurrentPersonalTitle($currentPersonalTitle);
    }

    public function getCurrentPersonalTitle()
    {
        $this->__load();
        return parent::getCurrentPersonalTitle();
    }

    public function setCurrentSuffix($currentSuffix)
    {
        $this->__load();
        return parent::setCurrentSuffix($currentSuffix);
    }

    public function getCurrentSuffix()
    {
        $this->__load();
        return parent::getCurrentSuffix();
    }

    public function setCurrentNickname($currentNickname)
    {
        $this->__load();
        return parent::setCurrentNickname($currentNickname);
    }

    public function getCurrentNickname()
    {
        $this->__load();
        return parent::getCurrentNickname();
    }

    public function setGender($gender)
    {
        $this->__load();
        return parent::setGender($gender);
    }

    public function getGender()
    {
        $this->__load();
        return parent::getGender();
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


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'currentLastName', 'currentFirstName', 'currentMiddleName', 'currentPersonalTitle', 'currentSuffix', 'currentNickname', 'gender', 'comment');
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