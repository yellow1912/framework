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
use \Doctrine\Common\Collections\Collection;

use Zepluf\Bundle\StoreBundle\Events\PaymentEvents;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Zepluf\Bundle\StoreBundle\Entity\Payment as PaymentEntity;

/**
*
*/
class PaypalStandard extends PaymentMethodAbstract implements PaymentMethodInterface
{
    /**
     * @var [type]
     */
    protected $settings;

    protected $templating;

    protected $entityManager;

    protected $eventDispatcher;

    function __construct($templating, EntityManager $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->templating = $templating;

        $this->entityManager = $entityManager;

        $this->eventDispatcher = $eventDispatcher;

        /**
         * @todo get current payment method settings from it's storage handler
         */
        $this->settings = array(
            'code' => 'paypal_standard',
            'sandbox_mode' => 0,
            'email' => 'seller.1314@yahoo.com',
            'status' => 1,
            'sort_order' => 20,
            'order_status' => array(
                'Canceled_Reversal' => 1,
                'Completed'         => 1,
                'Denied'            => 1,
                'Expired'           => 1,
                'Failed'            => 1,
                'Pending'           => 1,
                'Processed'         => 1,
                'Refunded'          => 1,
                'Reversed'          => 1,
                'Voided'            => 1
            )
        );
    }

    /**
     * get settings from this payment method
     *
     * @param   string|null  $key  setting key | null
     * @return  mixed              setting values | false
     */
    public function getSettings($key = null)
    {
        if (null === $key) {
            return $this->settings;
        } else if (isset($this->settings[$key])) {
            return $this->settings[$key];
        } else {
            return false;
        }
    }

    /**
     * check current payment method is active or inactive
     *
     * @return boolean
     */
    public function isAvailable()
    {
        if (isset($this->settings['status']) && $this->settings['status']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check all current payment method conditions are passed
     *
     * @return boolean
     */
    public function checkCondition()
    {
        // TODO:
        // get and check all conditions for this payment method are passed
        // with contact mechanism, order items, shipping method
        return true;
    }

    /**
     * [renderSelection description]
     *
     * @return [type] [description]
     */
    public function renderSelection()
    {

    }

    /**
     * [renderSelection description]
     *
     * @return [type] [description]
     */
    public function renderForm(Collection $invoiceItems)
    {
        if ($invoiceItems->isEmpty()) {
            // TODO: redirect to home page
            throw new Exception('No items available..', 1);
        }

        $data['sandbox_mode'] = $this->settings['sandbox_mode'];
        $data['sandbox_notify'] = 'Sandbox notify';

        if ($data['sandbox_mode']) {
            $data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
        }

        $data['business'] = $this->settings['email'];

        $data['products'] = array();
        while (false !== ($invoiceItem = $invoiceItems->next())) {
            $featuresValueIds = $invoiceItem->getInventoryItem()->getFeatureValueIds();
            $features = $this->entityManager->createQueryBuilder()
               ->select(array('pf.name', 'pfv.value'))
               ->from('Zepluf\Bundle\StoreBundle\Entity\ProductFeatureValue', 'pfv')
               ->leftJoin('Zepluf\Bundle\StoreBundle\Entity\ProductFeature', 'pf', 'WITH', 'pfv.product_feature_id = pf.id')
               ->where('pfv.id IN (' . $featuresValueIds . ')');

            $data['products'][] = array(
                'name'       => $invoiceItem->getItemDescription(),
                'price'      => $invoiceItem->getAmount(),
                'quantity'   => $invoiceItem->getQuantity(),
                'features'   => $features
            );
        }

        return $this->templating->render('StoreBundle:fontend/component/payment/paypal_standard.html.php', $data);
    }

    /**
     * [renderSelection description]
     *
     * @return [type] [description]
     */
    public function renderSubmit()
    {

    }

    /**
     * validation form data
     *
     * @return boolean
     */
    public function validation()
    {
        return true;
    }

    /**
     * [callback description]
     * @param  [type]   $data [description]
     * @return function       [description]
     */
    public function callback($data)
    {
        $this->eventDispatcher->dispatch(PaymentEvents::onPaypalStandardCallbackStart, $orderStatusId);

        if (true === isset($data['custom']) && true === ($paymentEntity = $this->entityManager->find('Zepluf\Bundle\StoreBundle\Entity\Payment', $data['custom']))) {
            $request['cmd'] = '_notify-validate';

            foreach ($data as $key => $value) {
                $request[$key] = $value;
            }

            if ($data['sandbox_mode']) {
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


            if ((0 === strcmp($response, 'VERIFIED') || 0 === strcmp($response, 'UNVERIFIED')) && true === isset($this->request->post['payment_status'])) {
                if (true === in_array($data['payment_status'], array_keys($this->settings['order_status']))) {
                    $orderStatusId = $this->settings['order_status'][$data['payment_status']];
                } else {
                    $orderStatusId = 1;
                }

                // if payment status is completed, recheck receiver email and amount
                // to make sure everything completely matched
                if (strtolower(trim($data['receiver_email'])) !== strtolower(trim($this->settings['email'])) || (float)$data['mc_gross'] !== $paymentEntity->getAmount()) {
                    throw new Exception('Paypal Standard :: Receiver email mismatch!');
                }

                // if (!$order_info['order_status_id']) {
                //     $this->model_checkout_order->confirm($order_id, $orderStatusId);
                // } else {
                //     $this->model_checkout_order->update($order_id, $orderStatusId);
                // }
            } else {
                // $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
            }

            // TODO: dispatch end "Paypal Standard Callback" event
            $this->eventDispatcher->dispatch(PaymentEvents::onPaypalStandardCallbackEnd, $orderStatusId);
        }
    }
}