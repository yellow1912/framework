<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Tests\Component\Payment;

use \Zepluf\Bundle\StoreBundle\Tests\BaseTestCase;

use Zepluf\Bundle\StoreBundle\Component\Payment\Fixtures;
use Zepluf\Bundle\StoreBundle\Component\Payment\Payment as PaymentComponent;

class PaymentTest extends BaseTestCase
{
    protected $fixtures;
    protected $payment;
    protected $invoice;

    public function setup()
    {
        $this->fixtures = new Fixtures($this->_container->get('doctrine'));
        $this->fixtures->setup();

        $this->payment = new PaymentComponent($this->_container->get('doctrine')->getEntityManager(), $this->_container->get('event_dispatcher'));

        // $this->invoice = $this->getMock('StoreBundle:Invoice');
    }

    public function tearDown()
    {
        $this->fixtures->tearDown();
    }

    public function testCreatePayment()
    {

    }
}
