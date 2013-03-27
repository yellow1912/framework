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
use \Doctrine\Common\Collections\ArrayCollection;

use Zepluf\Bundle\StoreBundle\Component\Payment\PaymentMethods;

use Zepluf\Bundle\StoreBundle\Component\Payment\Payment as PaymentComponent;
use Zepluf\Bundle\StoreBundle\Component\Invoice\Invoice as InvoiceComponent;

class PaymentMethodsTest extends BaseTestCase
{
    protected $entityManager;
    protected $eventDispatcher;

    protected $paymentEntity;
    protected $PaymentComponent;

    protected $invoiceCollection;

    public function setup()
    {
    }

    public function tearDown()
    {
    }

    public function testLogic()
    {
        $this->paymentMethods = new PaymentMethods();
        $paypalStandard = $this->getMockBuilder('Zepluf\Bundle\StoreBundle\Component\Payment\Method\PaypalStandard')->disableOriginalConstructor()->getMock();
        $cheque = $this->getMockBuilder('Zepluf\Bundle\StoreBundle\Component\Payment\Method\Cheque')->disableOriginalConstructor()->getMock();

        $paypalStandard->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $paypalStandard->expects($this->any())
            ->method('getCode')
            ->will($this->returnValue('paypal_standard'));

        $cheque->expects($this->any())
            ->method('isAvailable')
            ->will($this->returnValue(false));

        // test add method
        $this->assertSame($this->paymentMethods, $this->paymentMethods->add($paypalStandard));
        $this->assertEquals(1, count($this->paymentMethods->get()));

        // test add unavailable method
        $this->assertSame($this->paymentMethods, $this->paymentMethods->add($cheque));
        $this->assertEquals(1, count($this->paymentMethods->get()));

        // test count methods
        $this->assertEquals(1, count($this->paymentMethods->get()));

        // test get method
        $this->assertSame($paypalStandard, $this->paymentMethods->get('paypal_standard'));
        $this->assertFalse($this->paymentMethods->get('cheque'));
    }

    public function testDatabase()
    {
        // real entity manager and entity test...
    }
}