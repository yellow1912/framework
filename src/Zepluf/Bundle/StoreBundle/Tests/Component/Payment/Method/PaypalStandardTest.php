<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Tests\Component\Payment\Method;

use Zepluf\Bundle\StoreBundle\Exceptions\PaymentException;
use Symfony\Component\Yaml\Parser;
use \Zepluf\Bundle\StoreBundle\Tests\BaseTestCase;
use \Doctrine\Common\Collections\ArrayCollection;

use Zepluf\Bundle\StoreBundle\Component\Payment\Fixtures;

use Zepluf\Bundle\StoreBundle\Component\Payment\Method\PaypalStandard;




class PaypalStandardTest extends BaseTestCase
{
    protected $entityManager;
    protected $eventDispatcher;
    protected $yaml;

    protected $paypal;

    public function setup()
    {
        // $this->entityManager = $this->_container->get('doctrine')->getEntityManager();
        // $this->eventDispatcher = $this->_container->get('event_dispatcher');

        $this->entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->paypal = new PaypalStandard($this->entityManager, $this->eventDispatcher);

        $parser = new Parser();

        $yamlFile = str_replace('Tests\Component', 'Component', __DIR__) . '/config/paypal_standard.yml';

        if (file_exists($yamlFile)) {
            $this->yaml = new Parser();
            $this->yaml = $this->yaml->parse(file_get_contents($yamlFile));
        } else {
            $this->fail('Please correct this path and try again... or disable this test in: ' . __FILE__ . ' at line: ' . __LINE__);
            return;
        }
    }

    public function tearDown()
    {

    }

    public function testGetCode()
    {
        $this->assertEquals('paypal_standard', $this->paypal->getCode());
    }

    public function testGetStatus()
    {
        $this->assertTrue($this->paypal->isAvailable());
    }

    public function testGetConfig()
    {
        // with null params
        $this->assertInternalType('array', $this->paypal->getConfig());
        $this->assertArrayHasKey('business', $this->paypal->getConfig());
        $this->assertArrayHasKey('sandbox_mode', $this->paypal->getConfig());
        $this->assertArrayHasKey('status', $this->paypal->getConfig());

        // with right keys
        $this->assertInternalType('string', $this->paypal->getConfig('business'));
        $this->assertInternalType('integer', $this->paypal->getConfig('sandbox_mode'));
        $this->assertInternalType('integer', $this->paypal->getConfig('status'));
        $this->assertInternalType('array', $this->paypal->getConfig('order_status'));

        // with fake keys
        $this->assertFalse($this->paypal->getConfig(0));
        $this->assertFalse($this->paypal->getConfig(true));
        $this->assertFalse($this->paypal->getConfig(false));
        $this->assertFalse($this->paypal->getConfig('(^__^)'));
    }

    /**
     * test for payment applied for only 1 invoice
     * and this invoice paid one time completely
     */
    public function testRenderFormWithSingleInvoiceCompletely()
    {
        $payment = $this->getMockBuilder('\Zepluf\Bundle\StoreBundle\Component\Payment\Payment')->disableOriginalConstructor()->getMock();
        $paymentEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Payment');
        $paymentApplication = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\PaymentApplication');
        $invoice = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Invoice');
        $paymentEntityId = mt_rand(1, 9999);

        $paymentApplications = new ArrayCollection();
        $invoiceItems = new ArrayCollection();

        $total = 0; $invoices = mt_rand(2, 8);
        for ($i = 1; $i <= $invoices; $i++) {
            $amount = mt_rand(10, 100);
            $quantity = mt_rand(1, 10);
            $total += $amount * $quantity;

            $invoiceItem = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\InvoiceItem');

            $invoiceItem->expects($this->any())
                ->method('getAmount')
                ->will($this->returnValue($amount));

            $invoiceItem->expects($this->any())
                ->method('getQuantity')
                ->will($this->returnValue($quantity));

            $invoiceItem->expects($this->any())
                ->method('getItemDescription')
                ->will($this->returnValue('Item desc ' . $i));

            $invoiceItems->add($invoiceItem);
        }

        $invoice->expects($this->any())
            ->method('getInvoiceItems')
            ->will($this->returnValue($invoiceItems));

        $paymentEntity->expects($this->any())
            ->method('getAmount')
            ->will($this->returnValue($total));

        $paymentApplication->expects($this->any())
            ->method('getInvoice')
            ->will($this->returnValue($invoice));

        $paymentApplications->add($paymentApplication);

        $paymentEntity->expects($this->any())
            ->method('getPaymentApplications')
            ->will($this->returnValue($paymentApplications));

        $paymentEntity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($paymentEntityId));

        $payment->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($paymentEntity));

        $formData = $this->paypal->renderForm($payment);

        // important
        $this->assertEquals($paymentEntityId, $formData['custom']);

        $this->assertEquals($this->yaml['currency_code'], $formData['currency_code']);
        $this->assertEquals($this->yaml['business'], $formData['business']);
        $this->assertEquals($this->yaml['notify_url'], $formData['notify_url']);
        $this->assertEquals($this->yaml['cancel_return'], $formData['cancel_return']);
        $this->assertEquals($paymentEntityId, $formData['custom']);

        $this->assertEquals('_cart', $formData['cmd']);
        $this->assertEquals($invoices, count($formData['items']));

        foreach ($invoiceItems as $index => $invoiceItem) {
            $this->assertEquals($invoiceItem->getItemDescription(), $formData['items'][$index]['item_name_' . ($index + 1)]);
            $this->assertEquals($invoiceItem->getAmount(), $formData['items'][$index]['amount_' . ($index + 1)]);
            $this->assertEquals($invoiceItem->getQuantity(), $formData['items'][$index]['quantity_' . ($index + 1)]);

            $this->assertEquals($total, $formData['total_amount']);
        }
    }

    /**
     * test for payment applied for only 1 invoice
     * and this invoice paid multi times
     */
    public function testRenderFormWithSingleInvoiceMultiTimes()
    {
    }



    /**
     * test for payment applied for multi invoices
     */
    public function testRenderFormWithMultiInvoices()
    {
    }

    /**
     * valid callback
     */
    public function testCallback_1()
    {
        $dataCallbackCompleted = __DIR__ . '/DataTest/dataCallbackCompleted.txt';

        if (!file_exists($dataCallbackCompleted)) {
            $this->fail('Please correct this path and try again... or disable this test in: ' . __FILE__);
            return;
        }

        try {
            $dataCallbackCompleted = unserialize(file_get_contents($dataCallbackCompleted));
        } catch (\Exception $e) {
            $this->fail('Invalid data callback resource...');
            return;
        }

        $paymentEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Payment');


        $paymentEntity->expects($this->any())
            ->method('getAmount')
            ->will($this->returnCallback(array($this, 'getAmountCallback')));

        $this->entityManager->expects($this->any())
            ->method('find')
            ->will($this->returnValue($paymentEntity));


        // test valid payment
        $this->test = null;
        $this->assertTrue($this->paypal->callback($dataCallbackCompleted));

        // test invalid requrest
        try {
            $this->assertTrue($this->paypal->callback(array()));
        } catch (PaymentException $pe) {
            $this->assertEquals(PaymentException::INVALID_REQUEST, $pe->getCode());
            return;
        }

        // test amount mismatch
        $this->test = 'amount mismatch';
        try {
            $this->assertTrue($this->paypal->callback($dataCallbackCompleted));
        } catch (PaymentException $pe) {
            $this->assertEquals(PaymentException::AMOUNT_MISMATCH, $pe->getCode());
            return;
        }

        // test invalid response
        try {
            $this->assertTrue($this->paypal->callback($dataCallbackCompleted));
        } catch (PaymentException $pe) {
            $this->assertEquals(PaymentException::INVALID_RESPONSE, $pe->getCode());
            return;
        }

        // test email mismatch
        $dataCallbackCompleted['receiver_email'] = null;
        try {
            $this->assertTrue($this->paypal->callback($dataCallbackCompleted));
        } catch (PaymentException $pe) {
            $this->assertEquals(PaymentException::EMAIL_MISMATCH, $pe->getCode());
            return;
        }
    }

    public function getAmountCallback()
    {
        if ('amount mismatch' === $this->test) {
            return -1;
        } else {
            return 2685.00;
        }
    }

    /**
     * invalid callback - total amount mismatch
     */
    public function testCallback_2()
    {
        // $dataCallbackCompleted = __DIR__ . '/DataTest/dataCallbackCompleted.txt';

        // if (!file_exists($dataCallbackCompleted)) {
        //     $this->fail('Please correct this path and try again... or disable this test in: ' . __FILE__);
        //     return;
        // }

        // try {
        //     $dataCallbackCompleted = unserialize(file_get_contents($dataCallbackCompleted));
        // } catch (\Exception $e) {
        //     $this->fail('Invalid data callback resource...');
        //     return;
        // }

        // $paymentEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Payment');

        // $paymentEntity->expects($this->any())
        //     ->method('getAmount')
        //     ->will($this->returnValue(-1));

        // $this->entityManager->expects($this->any())
        //     ->method('find')
        //     ->will($this->returnValue($paymentEntity));

        // try {
        //     $this->assertTrue($this->paypal->callback($dataCallbackCompleted));
        // } catch (PaymentException $pe) {
        //     $this->assertEquals(PaymentException::AMOUNT_MISMATCH, $pe->getCode());
        //     return;
        // }

        // try {
        //     $this->assertTrue($this->paypal->callback(array()));
        // } catch (PaymentException $pe) {
        //     $this->assertEquals(PaymentException::INVALID_REQUEST, $pe->getCode());
        //     return;
        // }
    }
}
