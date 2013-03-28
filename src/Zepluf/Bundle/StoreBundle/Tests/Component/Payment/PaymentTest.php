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

use Zepluf\Bundle\StoreBundle\Component\Payment\Fixtures;
use Zepluf\Bundle\StoreBundle\Component\Payment\Payment as PaymentComponent;
use Zepluf\Bundle\StoreBundle\Component\Invoice\Invoice as InvoiceComponent;

class PaymentTest extends BaseTestCase
{
    protected $entityManager;
    protected $eventDispatcher;

    protected $paymentEntity;
    protected $PaymentComponent;

    protected $invoiceCollection;

    protected $invoices;

    public function setup()
    {
    }

    public function tearDown()
    {
    }

    public function testLogic()
    {
        $this->entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->paymentEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Payment');

        $this->PaymentComponent = new PaymentComponent($this->entityManager, $this->eventDispatcher);
        $this->invoiceCollection = new ArrayCollection();

        // test get null entity
        $this->assertNull($this->PaymentComponent->getEntity());

        // test set entity
        $this->assertSame($this->PaymentComponent, $this->PaymentComponent->setEntity($this->paymentEntity));

        // test get right entity
        $this->assertSame($this->paymentEntity, $this->PaymentComponent->getEntity());

        // test add payment applications
        $total = 0;
        for ($i = 1; $i <= 10; $i++) {
            $amountApplied = mt_rand(10, 100);

            $invoiceEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Invoice');

            $invoiceEntity->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($i));

            $invoiceEntity->expects($this->any())
                ->method('getAmountApplied')
                ->will($this->returnValue($amountApplied));

            $invoiceEntity->expects($this->any())
                ->method('getSequenceId')
                ->will($this->returnValue($i));

            $this->invoiceCollection->add($invoiceEntity);
        }

        $this->assertSame($this->PaymentComponent, $this->PaymentComponent->addPaymentApplication($this->invoiceCollection));
    }

    public function testDatabase()
    {
        // real entity manager and entity test...
    }
}
