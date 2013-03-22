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

use \Doctrine\Common\Collections\Collection;

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

    function __construct($templating)
    {
        parent::__construct();

        /**
         * @todo get current payment method settings from this storage handler
         */
        $this->settings = array(
            'code' => 'paypal_standard',
            'sandbox_mode' => 0,
            'email' => 'seller.1314@yahoo.com',
            'status' => 1,
            'sort_order' => 20
        );

        $this->templating = $templating;

        // echo '<strong>Paypal Standard</strong> loaded!<br />';

        // $this->renderForm();
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
        exit('OK');
        if ($invoiceItems->isEmpty()) {
            // TODO: redirect to home page
            echo 'No items available..';
            return;
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
            $featuresId = $invoiceItem->getInventoryItem()->getFeatureValueIds();
            $features = $this->entityManager->createQueryBuilder()
               ->select(array('pf.name', 'pfv.value'))
               ->from('Zepluf\Bundle\StoreBundle\Entity\ProductFeatureValue', 'pfv')
               ->leftJoin('Zepluf\Bundle\StoreBundle\Entity\ProductFeature', 'pf', 'WITH', 'pfv.product_feature_id = pf.id')
               ->where('pfv.id IN (' . $featuresId . ')');

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

    public function process($data, $amount, Collection $invoiceItems)
    {
        $invoice = $this->entityManager->find('', $invoiceId);

        $request = array(
            'cmd' => '',
            'business' => $this->settings['email'],
            'notify_url' => '',
            'return' => '',
            'cancel_return' => '',
            'paymentaction' => 'authorization'
        );

        $request = array_merge($request, $data);
    }
}