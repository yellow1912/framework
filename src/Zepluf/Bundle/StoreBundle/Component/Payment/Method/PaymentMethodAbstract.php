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

use Symfony\Component\Yaml\Parser;
use \Doctrine\Common\Collections\ArrayCollection;
use \Zepluf\Bundle\StoreBundle\Component\Payment\Payment;

abstract class PaymentMethodAbstract
{
    /**
     * payment method config
     * @var array
     */
    protected $config;

    /**
     * payment method identify code
     * @var string
     */
    protected $code = 'paypal_standard';

    function __construct()
    {
        $yamlFile = __DIR__ . '/config/' . $this->getCode() . '.yml';

        if (file_exists($yamlFile)) {
            $yaml = new Parser();
            $this->config = $yaml->parse(file_get_contents($yamlFile));
        }
    }

    /**
     * get payment method identify code
     *
     * @return string
     */
    function getCode()
    {
        return $this->code;
    }

    /**
     * get config from this payment method
     *
     * @param null $param
     * @return bool|mixed
     */
    public function getConfig($param = null)
    {
        if (null === $param) {
            return $this->config;
        } else if (isset($this->config[$param])) {
            return $this->config[$param];
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
        if ($this->getConfig('status')) {
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
     *
     */
    public function renderSelection()
    {
    }

    /**
     * @param ArrayCollection $invoiceItems
     */
    public function renderForm(Payment $payment)
    {
    }

    /**
     *
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
}