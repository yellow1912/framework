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

/**
*
*/
class Payment {
    /**
     * @var list of available payment methods
     */
    protected $paymentMethods = array();
    // protected $storageHandlers;

    public function __construct()
    {

    }

    public function addPaymentMethod(PaymentInterface $paymentMethod)
    {
        if (true === $paymentMethod->isAvailable()) {
            // print_r($paymentMethod);

            $this->paymentMethods[] = $paymentMethod;
        }
    }
}