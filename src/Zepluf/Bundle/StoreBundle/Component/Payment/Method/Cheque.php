<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Payment\Method;

use Zepluf\Bundle\StoreBundle\Component\Payment\PaymentAbstract;
use Zepluf\Bundle\StoreBundle\Component\Payment\PaymentInterface;

/**
*
*/
class Cheque extends PaymentAbstract implements PaymentInterface
{
    /**
     * @var [type]
     */
    protected $settings;

    /**
     * @var [type]
     */
    // protected $storateHandler;

    function __construct()
    {
        $this->settings = $this->getSettings();
    }

    /**
     * get current settings from this payment method
     *
     * @return array
     */
    public function getSettings()
    {
        /**
         * @todo get current payment method settings from this storage handler
         */
        return array(
            'status' => 1,
            'sort_order' => 10
        );
    }

    /**
     * set payment storate handler
     *
     * @param PaymentStorageHandlerInterface $storateHandler
     */
    // public function setStorateHandler(PaymentStorageHandlerInterface $storateHandler)
    // {
    //     $this->storateHandler = $storateHandler;
    // }

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
    public function renderForm()
    {
        return null;
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

    public function process()
    {

    }
}