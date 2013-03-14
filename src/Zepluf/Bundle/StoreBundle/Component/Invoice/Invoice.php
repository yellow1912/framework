<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Payment;

use \Doctrine\ORM\EntityManager;
use Zepluf\Bundle\StoreBundle\Entity\Invoice as InvoiceEntity;
use Zepluf\Bundle\StoreBundle\Entity\InvoiceItem as InvoiceItemEntity;

class Invoice
{
    /**
     * entity manager
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * invoice entity
     * @var InvoiceEntity
     */
    protected $invoice;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->invoice = new InvoiceEntity();
    }

    /**
     * create new invoice
     *
     * @param  array  $invoice_items array of invoice item, includes: id, name, quantity, features
     * @return [type]                [description]
     */
    public function create($invoice_items = array())
    {
        // set billed to \Zepluf\Bundle\StoreBundle\Entity\Party
        $this->invoice->setBilledTo(1);

        // set billed from \Zepluf\Bundle\StoreBundle\Entity\Party
        $this->invoice->setBilledFrom(1);

        // set addessed to \Zepluf\Bundle\StoreBundle\Entity\ContactMechanism
        $this->invoice->setAddressedTo(1);

        // set send to \Zepluf\Bundle\StoreBundle\Entity\ContactMechanism
        $this->invoice->setSentTo(1);

        // set entry date
        $this->invoice->setEntryDate(new \DateTime());

        $this->entityManager->persist($this->invoice);
        $this->entityManager->flush();

        $this->addInvoiceItems($invoice_items);
    }

    public function addInvoiceItems($invoice_items = array())
    {
        $invoiceId = $this->invoice->getId();

        /**
         * @var array
         * 'id' => integer
         */
        $item;
        foreach ($invoice_items as $item) {
            // find inventory item by id \Zepluf\Bundle\StoreBundle\Entity\InventoryItem
            $inventoryItem = $this->entityManager->find('\Zepluf\Bundle\StoreBundle\Entity\InventoryItem', $item['id']);

            if ($inventoryIte) {
                $invoiceItem = new InvoiceItemEntity();

                // link to invoice
                $invoiceItem->setInvoice($this->invoice);

                // link to inventory item
                $invoiceItem->setInventoryItem($inventoryIte);

                // link to adjustment type \Zepluf\Bundle\StoreBundle\Entity\AdjustmentType
                $invoiceItem->setAdjustmentType(1);

                // link to invoice item type \Zepluf\Bundle\StoreBundle\Entity\InvoiceItemType
                $invoiceItem->setInvoiceItemType(1);

                // set quantity
                $invoiceItem->setQuantity(1);

                // set amount
                $invoiceItem->setAmount(9.75);

                // set taxable
                $invoiceItem->setIsTaxable(1);
            }

        }
    }
}