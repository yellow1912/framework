<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Zepluf\Bundle\StoreBundle\Component\Invoice;

use \Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use \Doctrine\Common\Collections\ArrayCollection;

use Zepluf\Bundle\StoreBundle\Entity\Party;
use Zepluf\Bundle\StoreBundle\Entity\ContactMechanism;
use Zepluf\Bundle\StoreBundle\Entity\Invoice as InvoiceEntity;
use Zepluf\Bundle\StoreBundle\Entity\InvoiceItem as InvoiceItemEntity;

use Zepluf\Bundle\StoreBundle\Component\Payment\Fixtures;
use Zepluf\Bundle\StoreBundle\Component\Payment\Method\PaypalStandard;

class Invoice
{
    /**
     * entity manager
     * @var EntityManager
     */
    protected $entityManager;

    protected $eventDispatcher;

    protected $templating;

    protected $invoice;

    protected $total = 0;

    /**
     * constructor
     *
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;

        $this->dispatcher = $eventDispatcher;
    }

    public function getEntity()
    {
        return $this->invoice;
    }

    /**
     * @param InvoiceEntity $invoice
     * @return $this
     */
    public function setEntity(InvoiceEntity $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * create new invoice from order items collection
     *
     * @return  \Zepluf\Bundle\StoreBundle\Entity\Invoice
     */
    public function save()
    {
        // begin transaction before flush anything into database
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($this->invoice);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();

            throw $e;
        }

        return $this->invoice;
    }

    /**
     * create invoice items from order items collection
     *
     * @param  ArrayCollection  $orderItems  a doctrine collection of order items
     * @return void
     */
    public function addInvoiceItems(ArrayCollection $invoiceItems)
    {
        foreach ($invoiceItems as $item) {
            $invoiceItem = new InvoiceItemEntity();

            $invoiceItem
                ->setInvoice($this->invoice)
                ->setItemDescription($item->getItemDescription())
                ->setType($item->getType())
                ->setQuantity($item->getQuantity())
                ->setAmount($item->getAmount())         // TODO: Set amount applied for this item.
                ->setIsTaxable($item->getIsTaxable())   // TODO: Set taxable for this item.
                ->setInventoryItem($item->getInventoryItem())
                ->setAdjustmentType($item->getAdjustmentType())
                ->setInvoiceItemType($item->getInvoiceItemType());

            $this->invoice->addInvoiceItem($invoiceItem);
        }

        return $this;
    }

    /**
     * calculate total invoice items amount
     *
     * @return  float
     */
    public function getTotal()
    {
        foreach ($this->invoice->getInvoiceItems() as $invoiceItem) {
            $this->total += $invoiceItem->getAmount() * $invoiceItem->getQuantity();
        }

        return $this->total;
    }
}