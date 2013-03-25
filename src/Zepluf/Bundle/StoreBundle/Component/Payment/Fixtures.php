<?php

namespace Zepluf\Bundle\StoreBundle\Component\Payment;

use Zepluf\Bundle\StoreBundle\Entity\PaymentMethodType;
use Zepluf\Bundle\StoreBundle\Entity\InvoiceItemType;
use Zepluf\Bundle\StoreBundle\Entity\AdjustmentType;

use Zepluf\Bundle\StoreBundle\Entity\ContactMechanismType;
use Zepluf\Bundle\StoreBundle\Entity\ContactMechanism;

use Zepluf\Bundle\StoreBundle\Entity\Person;
use Zepluf\Bundle\StoreBundle\Entity\Party;

use Zepluf\Bundle\StoreBundle\Entity\UnitOfMeasurement;
use Zepluf\Bundle\StoreBundle\Entity\Product;

use Zepluf\Bundle\StoreBundle\Entity\Lot;
use Zepluf\Bundle\StoreBundle\Entity\Facility;
use Zepluf\Bundle\StoreBundle\Entity\ContainerType;
use Zepluf\Bundle\StoreBundle\Entity\Container;
use Zepluf\Bundle\StoreBundle\Entity\Location;

use Zepluf\Bundle\StoreBundle\Entity\InventoryItemStatusType;
use Zepluf\Bundle\StoreBundle\Entity\InventoryItem;



class Fixtures {
    private $entityManager;

    public function __construct($doctrine)
    {
        $this->entityManager = $doctrine->getEntityManager();
    }


    public function setup()
    {
        foreach (get_class_methods($this) as $method) {
            if (0 === strpos($method, 'generate_')) {
                $this->{$method}();
            }
        }
    }


    public function tearDown($tables = null)
    {
        if (null === $tables) {
            foreach (get_class_methods($this) as $method) {
                if (0 === strpos($method, 'generate_')) {
                    $this->truncate(substr($method, 9));
                }
            }
        } else if (is_string($tables)) {
            $this->truncate($tables);
        } else if (is_array($tables)) {
            foreach ($tables as $table) {
                $this->truncate($table);
            }
        }
    }


    public function truncate($table)
    {
        $connection = $this->entityManager->getConnection();

        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($table);
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    private function generate_payment_method_type()
    {
        for ($i = 1; $i <= 5; $i++) {
            $paymentMethodType = new PaymentMethodType();

            $paymentMethodType->setDescription('Payment Method Type ' . $i);

            $this->entityManager->persist($paymentMethodType);
        }

        $this->entityManager->flush();
    }


    private function generate_invoice_item_type()
    {
        for ($i = 1; $i <= 5; $i++) {
            $invoiceItemType = new InvoiceItemType();

            $invoiceItemType->setDescription('Invoice Item Type ' . $i);

            $this->entityManager->persist($invoiceItemType);
        }

        $this->entityManager->flush();
    }


    private function generate_adjustment_type()
    {
        for ($i = 1; $i <= 5; $i++) {
            $adjustmentType = new AdjustmentType();

            $adjustmentType->setDescription('Adjustment Type ' . $i);

            $this->entityManager->persist($adjustmentType);
        }

        $this->entityManager->flush();
    }


    private function generate_contact_mechanism_type()
    {
        for ($i = 1; $i <= 5; $i++) {
            $contactMechanismType = new ContactMechanismType();

            $contactMechanismType->setDescription('Contact Mechanism Type ' . $i);

            $this->entityManager->persist($contactMechanismType);
        }

        $this->entityManager->flush();
    }


    private function generate_contact_mechanism()
    {
        for ($i = 1; $i <= 5; $i++) {
            $contactMechanismType = $this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\ContactMechanismType', $i);
            $contactMechanism = new ContactMechanism();

            $contactMechanism->setContactMechanismType($contactMechanismType);
            $contactMechanism->setAddress1('Contact Mechanism ' . $i . ' - Address 01');
            $contactMechanism->setAddress2('Contact Mechanism ' . $i . ' - Address 02');
            $contactMechanism->setCity('Contact Mechanism ' . $i . ' - City');

            $this->entityManager->persist($contactMechanism);
        }

        $this->entityManager->flush();
    }


    private function generate_person()
    {
        for ($i = 1; $i <= 5; $i++) {
            $person = new Person();

            $person->setCurrentLastName('Last Name ' . $i);
            $person->setCurrentFirstName('First Name ' . $i);
            $person->setCurrentMiddleName('Middle Name ' . $i);

            $this->entityManager->persist($person);
        }

        $this->entityManager->flush();
    }

    private function generate_party()
    {
        for ($i = 1; $i <= 5; $i++) {
            $party = new Party();
            $person = $this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Person', $i + 1);

            $party->setPerson($person);
            $party->setDescription('Party description ' . $i);
            $party->setType(true);

            $this->entityManager->persist($party);
        }

        $this->entityManager->flush();
    }


    private function generate_unit_of_measurement()
    {
        for ($i = 1; $i <= 5; $i++) {
            $unitOfMeasurement = new UnitOfMeasurement();

            $unitOfMeasurement->setDescription('Unit of measurement description ' . $i);
            $unitOfMeasurement->setAbbreviation('Unit of measurement abbreviation ' . $i);

            $this->entityManager->persist($unitOfMeasurement);
        }

        $this->entityManager->flush();
    }


    private function generate_product()
    {
        for ($i = 1; $i <= 5; $i++) {
            $product = new Product();

            $product->setUnitOfMeasurement($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\UnitOfMeasurement', $i));
            $product->setType($i);
            $product->setName('Product ' . $i);
            $product->setIntroductionDate(new \DateTime());

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }


    private function generate_lot()
    {
        for ($i = 1; $i <= 5; $i++) {
            $lot = new Lot();

            $this->entityManager->persist($lot);
        }

        $this->entityManager->flush();
    }


    private function generate_facility()
    {
        for ($i = 1; $i <= 5; $i++) {
            $facility = new Facility();

            $this->entityManager->persist($facility);
        }

        $this->entityManager->flush();
    }


    private function generate_container_type()
    {
        for ($i = 1; $i <= 5; $i++) {
            $containerType = new ContainerType();
            $containerType->setName('Container type ' . $i);

            $this->entityManager->persist($containerType);
        }

        $this->entityManager->flush();
    }


    private function generate_container()
    {
        for ($i = 1; $i <= 5; $i++) {
            $container = new Container();
            $container->setContainerType($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\ContainerType', $i));
            $container->setFacility($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Facility', $i));

            $this->entityManager->persist($container);
        }

        $this->entityManager->flush();
    }


    private function generate_location()
    {
        for ($i = 1; $i <= 5; $i++) {
            $location = new Location();
            $location->setContainer($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Container', $i));
            $location->setFacility($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Facility', $i));
            $location->setLot($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Lot', $i));

            $this->entityManager->persist($location);
        }

        $this->entityManager->flush();
    }


    private function generate_inventory_item_status_type()
    {
        for ($i = 1; $i <= 5; $i++) {
            $inventoryItemStatusType = new InventoryItemStatusType();

            $inventoryItemStatusType->setName('Inventory item status type name ' . $i);
            $inventoryItemStatusType->setDescription('Inventory item status type description ' .$i);

            $this->entityManager->persist($inventoryItemStatusType);
        }

        $this->entityManager->flush();
    }


    private function generate_inventory_item()
    {
        for ($i = 1; $i <= 5; $i++) {
            $inventoryItem = new InventoryItem();

            $inventoryItem->setProduct($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Product', $i));
            $inventoryItem->setInventoryItemStatusType($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\InventoryItemStatusType', $i));
            $inventoryItem->setLocation($this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Location', $i));
            $inventoryItem->setQuantityOnhand($i);

            $this->entityManager->persist($inventoryItem);
        }

        $this->entityManager->flush();
    }
}