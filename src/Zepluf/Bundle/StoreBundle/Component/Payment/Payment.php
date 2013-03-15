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

use Zepluf\Bundle\StoreBundle\Component\Payment\PaymentMethodInterface;
use Zepluf\Bundle\StoreBundle\Component\Invoice\Invoice;

use Zepluf\Bundle\StoreBundle\Entity\Payment as PaymentEntity;
use Zepluf\Bundle\StoreBundle\Entity\PaymentApplication as PaymentApplicationEntity;
use Zepluf\Bundle\StoreBundle\Entity\Invoice as InvoiceEntity;
use Zepluf\Bundle\StoreBundle\Entity\InvoiceItem as InvoiceItemEntity;

/**
*
*/
class Payment
{
    protected $entityManager;

    /**
     * payment entity
     * @var PaymentEntity
     */
    protected $payment = false;

    /**
     * constructor
     * @param EntityManager $entityManager
     */
    public function __construct()
    {
        die('abc');
        $this->entityManager = $doctrine->getEntityManager();

        print_r($this->entityManager);
        exit();
    }

    /**
     * @param array $data ('payment_method' => array(), 'invoice_items' => array(). ...)
     * @throws \Exception
     */
    public function create(PaymentMethodInterface $paymentMethod, InvoiceEntity $invoice)
    {
        $this->payment = new PaymentEntity();

        // set payment method type Zepluf\Bundle\StoreBundle\Entity\PaymentMethodType
        $this->payment->setPaymentMethodType(1);

        // set effective date
        $this->payment->setEffectiveDate(new \DateTime());

        // set payment type: receipt, disbursement
        $this->payment->setType(1);


        // get all invoice items
        $invoiceItems = $invoice->getInvoiceItems();

        foreach ($invoiceItems as $invoiceItem) {
            $paymentApplication = new PaymentApplicationEntity();

            $paymentApplication->setPayment($this->payment);
            $paymentApplication->setInvoiceItem($invoiceItem);
        }
    }

// cai inventory, invoice_status_type, product_association_type, inventory_item_variance, inventory_item_variance_reason, inventory_item_status_type, container, facility, container_type, party_type thieu auto increment
}