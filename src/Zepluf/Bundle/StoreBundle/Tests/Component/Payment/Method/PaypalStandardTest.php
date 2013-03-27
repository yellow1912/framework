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
        $this->entityManager = $this->_container->get('doctrine')->getEntityManager();
        $this->eventDispatcher = $this->_container->get('event_dispatcher');



        $this->paypal = new PaypalStandard($this->entityManager, $this->eventDispatcher);

        $parser = new Parser();

        $yamlFile = 'D:/xampp/htdocs/framework/src/Zepluf/Bundle/StoreBundle/Component/Payment/Method/config/paypal_standard.yml';

        if (file_exists($yamlFile)) {
            $this->yaml = new Parser();
            $this->yaml = $this->yaml->parse(file_get_contents($yamlFile));
        } else {
            echo "\r\n\r\n--------------------------------------------------------------------------------\r\n";
            echo 'Paypal Standard config file "' . $yamlFile . '" can not be found!' . "\r\n\r\n";

            exit('Please correct this path and try again... or disable this test in: ' . __FILE__);
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

        $total = 0;
        for ($i = 1; $i <= 5; $i++) {
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

        $this->assertEquals('_cart', $formData['cmd']);
        $this->assertEquals(5, count($formData['items']));

        foreach ($invoiceItems as $index => $invoiceItem) {
            $this->assertEquals($invoiceItem->getItemDescription(), $formData['items'][$index]['item_name_' . $index]);
            $this->assertEquals($invoiceItem->getAmount(), $formData['items'][$index]['amount_' . $index]);
            $this->assertEquals($invoiceItem->getQuantity(), $formData['items'][$index]['quantity_' . $index]);

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


}
