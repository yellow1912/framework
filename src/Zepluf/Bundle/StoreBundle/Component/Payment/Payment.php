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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Zepluf\Bundle\StoreBundle\Component\Payment\PaymentMethodInterface;
use Zepluf\Bundle\StoreBundle\Component\Payment\Method\Cheque;

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
     * $entityManager a Doctrine entity manager
     *
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * $eventDispatcher Symfony event dispatcher
     *
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * $payment payment entity
     *
     * @var Zepluf\Bundle\StoreBundle\Entity\Payment
     */
    protected $payment;

    /**
     * constructor
     *
     * @param Doctrine\ORM\EntityManager $entityManager
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * [create description]
     *
     * @param  array  $data
     *
     * @return Zepluf\Bundle\StoreBundle\Component\Payment\Payment
     */
    public function create($data = array())
    {
        // begin transaction before flush anything into database
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->payment = new PaymentEntity();

            $this->payment
                ->setEffectiveDate(new \DateTime())
                ->setSequenceId($data['sequenceId'])
                ->setReferenceNumber($data['referenceNumber'])
                ->setAmount($data['amount'])
                ->setComment($data['comment'])
                ->setType($data['type']);

            $this->addPaymentApplication($data['invoices']);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();

            throw $e;
        }

        return $this->payment;
    }


    /**
     * create payment application from invoices list
     *
     * @param Zepluf\Bundle\StoreBundle\Entity\Invoice $invoices
     */
    public function addPaymentApplication(InvoiceEntity $invoices)
    {
        // TODO: loop invoice items
        foreach ($invoices as $invoice) {
            $paymentApplication = new PaymentApplicationEntity();

            $paymentApplication
                ->setAmountApplied($invoice->amountApplied)
                ->setSequenceId($this->payment->getSequenceId())
                ->setPayment($this->payment)
                ->setInvoice($invoice);

            $this->payment->addPaymentApplication($paymentApplication);

            $this->entityManager->persist($paymentApplication);
        }
    }
}