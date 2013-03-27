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

    // /**
    // * get identify code for this payment method
    // *
    // * @return string identify code
    // */
    // public function getCode()
    // {
    //     return $this->code;
    // }

    // /**
    // * get settings from this payment method
    // *
    // * @param   string|null  $key  setting key | null
    // * @return  mixed              setting values | false
    // */
    // public function getSettings($key = null)
    // {
    //     if (null === $key) {
    //         return $this->settings;
    //     } else if (isset($this->settings[$key])) {
    //         return $this->settings[$key];
    //     } else {
    //         return false;
    //     }
    // }

    // /**
    // * check current payment method is active or inactive
    // *
    // * @return boolean
    // */
    // public function isAvailable()
    // {
    //     if (isset($this->settings['status']) && $this->settings['status']) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    // /**
    // * check all current payment method conditions are passed
    // *
    // * @return boolean
    // */
    // public function checkCondition()
    // {
    //     // TODO:
    //     // get and check all conditions for this payment method are passed
    //     // with contact mechanism, order items, shipping method
    //     return true;
    // }

    // /**
    // * [renderSelection description]
    // *
    // * @return [type] [description]
    // */
    // public function renderSelection()
    // {

    // }

    // /**
    //  * [renderSelection description]
    //  *
    //  * @return [type] [description]
    //  */
    // public function renderSubmit()
    // {

    // }

    // /**
    //  * validation form data
    //  *
    //  * @return boolean
    //  */
    // public function validation()
    // {
    //     return true;
    // }

    /**
     * [renderForm description]
     *
     * @param  Payment $payment [description]
     * @return array            [description]
     */
    public function renderForm(Payment $payment)
    {
        // $data['business']      = $this->getConfig('business');
        // $data['currency_code'] = $this->getConfig('currency_code');
        // $data['notify_url']    = $this->getConfig('notify_url');
        // $data['return_url']    = $this->getConfig('return_url');
        // $data['cancel_return'] = $this->getConfig('cancel_return');

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
                $data['amount'] = $payment->getAmount();
                $data['item_name'] = 'Paying for invoice ID: ' . $invoice->getId();
            }
        }
        // this payment applied for multi invoices
        else {
            $data['cmd'] = '_cart';
            $data['amount'] = $payment->getAmount();

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

        // this content for test only
        file_put_contents(str_replace('Component', 'Tests/Component', __DIR__) . '/DataTest/cart_info.txt', serialize($data));
        return $data;

        // $data['sandbox_mode'] = $this->settings['sandbox_mode'];
        // $data['sandbox_notify'] = 'Sandbox notify';

        // if ($data['sandbox_mode']) {
        //     $data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        // } else {
        //     $data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
        // }

        // $data['business'] = $this->settings['email'];

        // $data['products'] = array();
        // while (false !== ($invoiceItem = $invoiceItems->next())) {
        //     $featuresValueIds = $invoiceItem->getInventoryItem()->getFeatureValueIds();
        //     $features = $this->entityManager->createQueryBuilder()
        //        ->select(array('pf.name', 'pfv.value'))
        //        ->from('Zepluf\Bundle\StoreBundle\Entity\ProductFeatureValue', 'pfv')
        //        ->leftJoin('Zepluf\Bundle\StoreBundle\Entity\ProductFeature', 'pf', 'WITH', 'pfv.product_feature_id = pf.id')
        //        ->where('pfv.id IN (' . $featuresValueIds . ')');

        //     $data['products'][] = array(
        //         'name'       => $invoiceItem->getItemDescription(),
        //         'price'      => $invoiceItem->getAmount(),
        //         'quantity'   => $invoiceItem->getQuantity(),
        //         'features'   => $features
        //     );
        // }

        // return $this->templating->render('StoreBundle:fontend/component/payment/paypal_standard.html.php', $data);
    }



    /**
     * [callback description]
     * @param  [type]   $data [description]
     * @return function       [description]
     */
    public function callback(array $data)
    {
        if (isset($data['custom']) && true == ($paymentEntity = $this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Payment', $data['custom']))) {
            $request['cmd'] = '_notify-validate';

            foreach ($data as $key => $value) {
                $request[$key] = $value;
            }

            if ($this->getConfig('sandbox_mode')) {
                $curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
            } else {
                $curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
            }

            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            try {
                $response = curl_exec($curl);
            } catch (\Exception $e) {
                throw $e;
            }

            curl_close($curl);

            if (0 === strcmp($response, 'VERIFIED')) {
                // recheck total amount to make sure everything completely matched
                if ((float)$data['mc_gross'] !== (float)$paymentEntity->getAmount()) {
                    throw new PaymentException('Paypal Standard :: Total amount mismatch!', PaymentException::AMOUNT_MISMATCH);
                }

                // recheck receiver email to make sure everything completely matched
                if (strtolower(trim($data['receiver_email'])) !== strtolower(trim($this->getConfig('business')))) {
                    throw new PaymentException('Paypal Standard :: Receiver email mismatch!', PaymentException::EMAIL_MISMATCH);
                }
            } else if (0 === strcmp($response, 'UNVERIFIED')) {
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