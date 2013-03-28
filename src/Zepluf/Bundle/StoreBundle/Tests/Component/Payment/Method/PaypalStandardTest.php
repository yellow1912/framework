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
        $this->assertEquals($this->yaml['status'], $this->paypal->isAvailable());
    }

    public function testGetConfig()
    {
        $paypalConfig = $this->paypal->getConfig();

        // test all valid config keys and values
        foreach ($this->yaml as $key => $value) {
            $this->assertArrayHasKey($key, $paypalConfig);
            $this->assertEquals($value, $this->paypal->getConfig($key));
        }

        // test with some invalid keys
        foreach (array(0, true, false, '(^__^)') as $key) {
            $this->assertFalse($this->paypal->getConfig($key));
        }
    }

    /**
     * test for payment applied for only 1 invoice
     * and this invoice paid one time completely
     */
    public function testRenderForm()
    {
        $payment = $this->getMockBuilder('\Zepluf\Bundle\StoreBundle\Component\Payment\Payment')->disableOriginalConstructor()->getMock();
        $paymentEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Payment');
        $paymentApplication = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\PaymentApplication');
        $invoice = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Invoice');
        $paymentEntityId = mt_rand(1, 9999);

        $paymentApplications = new ArrayCollection();
        $invoiceItems = new ArrayCollection();

        $total = 0; $items = mt_rand(2, 8);
        for ($i = 1; $i <= $items; $i++) {
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
        file_put_contents('DataTest/cart_info.txt', serialize($formData));

        // important
        $this->assertEquals($paymentEntityId, $formData['custom']);

        $this->assertEquals($this->yaml['currency_code'], $formData['currency_code']);
        $this->assertEquals($this->yaml['business'], $formData['business']);
        $this->assertEquals($this->yaml['notify_url'], $formData['notify_url']);
        $this->assertEquals($this->yaml['cancel_return'], $formData['cancel_return']);
        $this->assertEquals($paymentEntityId, $formData['custom']);

        $this->assertEquals('_cart', $formData['cmd']);
        $this->assertEquals($items, count($formData['items']));

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

    private function getCallbackData($state = 'completed')
    {
        $data = __DIR__ . '/DataTest/callback_' . $state . '.txt';

        if (!file_exists($data)) {
            $this->fail('File Not Found: ' . $data);
            return;
        }

        try {
            $data = unserialize(file_get_contents($data));
        } catch (\Exception $e) {
            $this->fail('Invalid data callback resource...');
            return;
        }

        return $data;
    }

    public function testCURL()
    {
        // exact sandbox url & params
        $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $params = $this->getCallbackData('completed');
        $params['cmd'] = '_notify-validate';

        $this->assertEquals('VERIFIED', $this->paypal->curl($url, $params));

        // exact sandbox url BUT fake params
        $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $params = array();
        $params['cmd'] = '_notify-validate';

        $this->assertEquals('INVALID', $this->paypal->curl($url, $params));

        // // fake url - DISABLED FOR SAVE TIME (LONG TIME RESPONSE)
        // $url = 'https://ruuuuubiiiiikiiiiin.cooooom';
        // $params = $this->getCallbackData('completed');
        // $params['cmd'] = '_notify-validate';

        // try {
        //     $this->paypal->curl($url, $params);
        // } catch (PaymentException $pe) {
        //     $this->assertEquals(PaymentException::CURL_EXCEPTION, $pe->getCode());
        // }

        // exact real url & fake params
        $url = 'https://www.paypal.com/cgi-bin/webscr';
        $params = array();
        $params['cmd'] = '_notify-validate';

        $this->assertEquals('INVALID', $this->paypal->curl($url, $params));
    }

    public function getPaymentAmountCallback()
    {
        return $this->paymentAmount;
    }

    /**
     * valid callback
     */
    public function testCallback()
    {
        $data = $this->getCallbackData('completed');

        $paymentEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Payment');

        $paymentEntity->expects($this->any())
            ->method('getAmount')
            ->will($this->returnCallback(array($this, 'getPaymentAmountCallback')));

        $this->entityManager->expects($this->any())
            ->method('find')
            ->will($this->returnValue($paymentEntity));

        // test valid payment
        $this->paymentAmount = 2685.00;
        $this->assertTrue($this->paypal->callback($data));

        // test invalid requrest
        try {
            $this->assertTrue($this->paypal->callback(array()));
        } catch (PaymentException $pe) {
            $this->assertEquals(PaymentException::INVALID_REQUEST, $pe->getCode());
        }

        // test amount mismatch
        $this->paymentAmount = -0.1314;
        try {
            $this->assertTrue($this->paypal->callback($data));
        } catch (PaymentException $pe) {
            $this->assertEquals(PaymentException::AMOUNT_MISMATCH, $pe->getCode());
        }

        // // test invalid response
        // try {
        //     $this->assertTrue($this->paypal->callback($data));
        // } catch (PaymentException $pe) {
        //     $this->assertEquals(PaymentException::INVALID_RESPONSE, $pe->getCode());
        // }
    }
}
