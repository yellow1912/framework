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

    protected $dispatcher;

    protected $templating;

    protected $invoice;

    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;

        $this->dispatcher = $dispatcher;

        // cleanup database;
        // $fixtures = new Fixtures($doctrine);
        // $this->templating = $templating;

        // $paypalStandard = new PaypalStandard();
        // $paypalStandard->renderForm(null, array());
        // $this->create();
    }

    /**
     * create new invoice from order items collection
     *
     * @param   array    $data
     * @return  boolean
     */
    public function create($data = array())
    {
        $this->invoice = new InvoiceEntity();

        $billedTo    = $this->entityManager->getReference('StoreBundle:Party', (int)$data['billedTo']);
        $billedFrom  = $this->entityManager->getReference('StoreBundle:Party', (int)$data['billedFrom']);
        $addressedTo = $this->entityManager->getReference('StoreBundle:ContactMechanism', (int)$data['addressedTo']);
        $sendTo      = $this->entityManager->getReference('StoreBundle:ContactMechanism', (int)$data['sendTo']);

        $this->invoice
            ->setBilledTo($billedTo)
            ->setBilledFrom($billedFrom)
            ->setAddressedTo($addressedTo)
            ->setSentTo($sendTo)
            ->setEntryDate(new \DateTime())
            ->setMessage($data['message'])
            ->setDescription($data['description']);

        return $this->addInvoiceItems($data['orderItems']);
    }

    /**
     * create invoice items from order items collection
     *
     * @param  ArrayCollection  $orderItems  a doctrine collection of order items
     * @return boolean
     */
    public function addInvoiceItems(ArrayCollection $orderItems)
    {
        foreach ($orderItems->getIterator() as $item) {
            $invoiceItem = new InvoiceItemEntity();

            $invoiceItem
                ->setItemDescription($item->getItemDescription())
                ->setType($item->getType())
                ->setQuantity($item->getQuantity())
                ->setAmount(0)      // TODO: Set amount applied for this item.
                ->setIsTaxable(0)   // TODO: Set taxable for this item.
                ->setInvoice($this->invoice)
                ->setInventoryItem($this->entityManager->getReference('StoreBundle:InventoryItem', 1))
                ->setAdjustmentType($this->entityManager->getReference('StoreBundle:AdjustmentType', 1))
                ->setInvoiceItemType($this->entityManager->getReference('StoreBundle:InvoiceItemType', 1));

            $this->invoice->addInvoiceItem($invoiceItem);

            $this->entityManager->persist($invoiceItem);
        }

        // begin transaction before flush anything into database
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

            return true;
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();

            throw $e;
        }

        return false;
    }
}