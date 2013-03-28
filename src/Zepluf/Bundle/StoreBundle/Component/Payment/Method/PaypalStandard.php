<?php
/**
 * Created by RubikIntegration Team.
 *
 * Date: 9/30/12
 * Time: 4:31 PM
 * Question? Come to our website at http://rubikin.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or refer to the LICENSE
 * file of ZePLUF
 */

namespace Zepluf\Bundle\StoreBundle\Component\Payment\Method;

use \Doctrine\ORM\EntityManager;
use \Doctrine\Common\Collections\ArrayCollection;
use Zepluf\Bundle\StoreBundle\Exceptions\PaymentException;

use Zepluf\Bundle\StoreBundle\Events\PaymentEvents;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use \Zepluf\Bundle\StoreBundle\Component\Payment\Payment;

/**
*
*/
class PaypalStandard extends PaymentMethodAbstract implements PaymentMethodInterface
{
    protected $code = 'paypal_standard';

    protected $templating;

    protected $entityManager;

    protected $eventDispatcher;

    function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();

        $this->entityManager = $entityManager;

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * [renderForm description]
     *
     * @param  Payment $payment [description]
     * @return array            [description]
     */
    public function renderForm(Payment $payment)
    {
        $data = array();
        foreach ($this->getConfig() as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $value;
            }
        }

        if ($this->getConfig('sandbox_mode')) {
            $data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
        }

        $paymentEntity = $payment->getEntity();
        $paymentApplications = $paymentEntity->getPaymentApplications();

        $data['custom'] = $paymentEntity->getId();

        // this payment applied for only 1 invoice
        if (1 === $paymentApplications->count()) {
            $invoice = $paymentApplications->current()->getInvoice();
            $invoiceItems = $invoice->getInvoiceItems();

            $totalAmount = 0;

            foreach ($invoiceItems as $invoiceItem) {
                $totalAmount += $invoiceItem->getAmount() * $invoiceItem->getQuantity();
            }

            $data['total_amount'] = $totalAmount;

            // this invoice paid one time completely
            if ($totalAmount === $paymentEntity->getAmount()) {
                $data['cmd'] = '_cart';

                foreach ($invoice->getInvoiceItems() as $index => $invoiceItem) {
                    // TODO: get features list

                    $data['items'][] = array(
                        'item_name_' . ($index + 1) => $invoiceItem->getItemDescription(),
                        'amount_' . ($index + 1) => $invoiceItem->getAmount(),
                        'quantity_' . ($index + 1) => $invoiceItem->getQuantity()
                    );
                }
            }
            // this invoice paid multi times
            else {
                $data['cmd'] = '_xclick';
                $data['amount'] = $paymentEntity->getAmount();
                $data['item_name'] = 'Paying for invoice ID: ' . $invoice->getId();
            }
        }
        // this payment applied for multi invoices
        else {
            $data['cmd'] = '_cart';
            $data['amount'] = $paymentEntity->getAmount();

            foreach ($paymentApplications as $paymentApplication) {
                $data['items'][] = array(
                    'item_name_' . $index => 'Invoice ID (' . $paymentApplication->getInvoice()->getId() . ')',
                    'amount_' . $index => $paymentApplication->getAmountApplied(),
                    'quantity_' . $index => 1
                );
            }
        }

        // TODO: Get infomation about customer: first_name, last_name, email, address, etc...

        // TODO: render paypal_standard template
        // return $this->templating->render('StoreBundle::fontend/component/payment:paypal_standard.html.php', $data);

        return $data;
    }


    public function curl($url, array $params = array())
    {
        $params = http_build_query($params);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        try {
            $response = curl_exec($ch);
        } catch (\Exception $e) {
            throw $e;
        }

        if (true == ($error = curl_error($ch))) {
            throw new PaymentException($error, PaymentException::CURL_EXCEPTION);
        }

        curl_close($ch);

        return $response;
    }


    /**
     * [callback description]
     * @param  [type]   $data [description]
     * @return function       [description]
     */
    public function callback(array $data)
    {
        if (isset($data['custom']) && true == ($paymentEntity = $this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Payment', $data['custom']))) {
            $params['cmd'] = '_notify-validate';

            if ($this->getConfig('sandbox_mode')) {
                $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else {
                $url = 'https://www.paypal.com/cgi-bin/webscr';
            }

            foreach ($data as $key => $value) {
                $params[$key] = $value;
            }

            $response = $this->curl($url, $params);
            if (0 === strcmp($response, 'VERIFIED')) {
                // recheck total amount to make sure everything completely matched
                if ((float)$data['mc_gross'] !== (float)$paymentEntity->getAmount()) {
                    throw new PaymentException('Paypal Standard :: Total amount mismatch!', PaymentException::AMOUNT_MISMATCH);
                }

                // recheck receiver email to make sure everything completely matched
                if (strtolower(trim($data['receiver_email'])) !== strtolower(trim($this->getConfig('business')))) {
                    throw new PaymentException('Paypal Standard :: Receiver email mismatch!', PaymentException::EMAIL_MISMATCH);
                }
            } else if (0 === strcmp($response, 'INVALID')) {
                return false;
            } else {
                throw new PaymentException('Paypal Standard :: Invalied response...', PaymentException::INVALID_RESPONSE);
            }

            // TODO: dispatch end "Paypal Standard Callback" event
            // $this->eventDispatcher->dispatch(PaymentEvents::onPaypalStandardCallbackEnd, $orderStatusId);
        } else {
            throw new PaymentException('Paypal Standard :: Invalid request...', PaymentException::INVALID_REQUEST);
        }

        return true;
    }
}