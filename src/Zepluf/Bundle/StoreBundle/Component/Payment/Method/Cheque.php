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

use Zepluf\Bundle\StoreBundle\Events\PaymentEvents;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use \Zepluf\Bundle\StoreBundle\Component\Payment\Payment;

/**
*
*/
class Cheque extends PaymentMethodAbstract implements PaymentMethodInterface
{
    protected $code = 'cheque';

    protected $entityManager;

    protected $eventDispatcher;

    function __construct()
    {
        $this->entityManager = $entityManager;

        $this->eventDispatcher = $eventDispatcher;
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
    public function renderForm(Payment $payment)
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
}