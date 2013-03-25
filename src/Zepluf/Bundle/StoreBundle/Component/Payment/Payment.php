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
use Zepluf\Bundle\StoreBundle\Entity\PaymentMethodType as PaymentMethodTypeEntity;
use Zepluf\Bundle\StoreBundle\Entity\PaymentApplication as PaymentApplicationEntity;
use Zepluf\Bundle\StoreBundle\Entity\Invoice as InvoiceEntity;

/**
*
*/
class Payment
{
    protected $entityManager;

    protected $dispatcher;

    /**
     * payment entity
     * @var PaymentEntity
     */
    protected $payment;

    /**
     * constructor
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;

        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array $data ('payment_method' => array(), 'invoiceItems' => array(). ...)
     * @throws \Exception
     */
    public function create($data = array())
    {
        $this->payment = new PaymentEntity();

        $this->payment
            ->setPaymentMethodType()
            ->setEffectiveDate(new \DateTime())
            ->setSequenceId($data['sequenceId'])
            ->setReferenceNumber($data['referenceNumber'])
            ->setAmount($data['amount'])
            ->setComment($data['comment'])
            ->setType($data['type']);

        return $this->addPaymentApplication($data['invoiceItems']);
    }


    /**
     * create payment application from invoice items collection
     *
     * @param ArrayCollection $invoiceItems [description]
     */
    public function addPaymentApplication(ArrayCollection $invoiceItems)
    {
        foreach ($invoiceItems->getIterator() as $item) {
            $paymentApplication = new PaymentApplicationEntity();

            $paymentApplication
                ->setAmountApplied($item['amountApplied'])
                ->setSequenceId($item['sequenceId'])
                ->setPayment($this->payment)
                ->setInvoice($this->entityManager->getReference('StoreBundle:Invoice'), (int)$item['invoiceItemId']);

            $this->payment->addPaymentApplication($paymentApplication);

            $this->entityManager->persist($paymentApplication);
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