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

use Doctrine\ORM\EntityManager;
use \Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Zepluf\Bundle\StoreBundle\Component\Payment\PaymentMethodInterface;

use Zepluf\Bundle\StoreBundle\Component\Invoice\Invoice;

use Zepluf\Bundle\StoreBundle\Entity\Payment as PaymentEntity;
use Zepluf\Bundle\StoreBundle\Entity\PaymentApplication as PaymentApplicationEntity;
use Zepluf\Bundle\StoreBundle\Entity\Invoice as InvoiceEntity;

/**
*
*/
class Payment
{
    /**
     * $entityManager Doctrine entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * $eventDispatcher Symfony event dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * $payment payment entity
     *
     * @var Payment
     */
    protected $payment;

    /**
     * constructor
     *
     * @param EntityManager $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;

        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEntity()
    {
        return $this->payment;
    }

    /**
     * set payment entity
     *
     * @param PaymentEntity $payment
     * @return $this
     */
    public function setEntity(PaymentEntity $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * create payment application from invoices collection
     *
     * @param ArrayCollection $invoices doctrine collection of invoices
     * @return $this
     */
    public function addPaymentApplication(ArrayCollection $invoices)
    {
        foreach ($invoices as $invoice) {
            $paymentApplication = new PaymentApplicationEntity();

            $paymentApplication
                ->setInvoice($invoice)
                ->setPayment($this->payment)
                ->setAmountApplied(1)
                ->setSequenceId($this->payment->getSequenceId());

            $this->payment->addPaymentApplication($paymentApplication);
        }

        return $this;
    }

    /**
     * save payment
     *
     * @return Payment
     * @throws \Exception
     */
    public function save()
    {
        // begin transaction before flush anything into database
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($this->payment);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();

            throw $e;
        }

        return $this->payment;
    }


//    /**
//     * [create description]
//     *
//     * @param  array  $data
//     *
//     * @return Payment
//     */
//    public function create($data = array())
//    {
//        // begin transaction before flush anything into database
//        $this->entityManager->getConnection()->beginTransaction();
//        try {
//            $this->payment = new PaymentEntity();
//
//            $this->payment
//                ->setEffectiveDate(new \DateTime())
//                ->setSequenceId($data['sequenceId'])
//                ->setReferenceNumber($data['referenceNumber'])
//                ->setAmount($data['amount'])
//                ->setComment($data['comment'])
//                ->setType($data['type']);
//
//            $this->entityManager->persist($this->payment);
//            $this->addPaymentApplication($data['invoices']);
//
//            $this->entityManager->flush();
//            $this->entityManager->getConnection()->commit();
//        } catch (\Exception $e) {
//            $this->entityManager->getConnection()->rollback();
//
//            throw $e;
//        }
//
//        return $this->payment;
//    }
//
//
//    /**
//     * create payment application from invoices list
//     *
//     * @param Invoice $invoices
//     */
//    public function addPaymentApplication(ArrayCollection $invoices)
//    {
//        // TODO: loop invoice items
//        foreach ($invoices->getIterator() as $invoice) {
//            $paymentApplication = new PaymentApplicationEntity();
//
//            $paymentApplication
//                ->setAmountApplied(1)
//                ->setSequenceId($this->payment->getSequenceId())
//                ->setPayment($this->payment)
//                ->setInvoice($invoice);
//
//            $this->payment->addPaymentApplication($paymentApplication);
//
//            $this->entityManager->persist($paymentApplication);
//        }
//    }
}