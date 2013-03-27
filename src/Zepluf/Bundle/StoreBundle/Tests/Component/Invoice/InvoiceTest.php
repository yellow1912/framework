<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Tests\Component\Invoice;

use \Zepluf\Bundle\StoreBundle\Tests\BaseTestCase;
use \Doctrine\Common\Collections\ArrayCollection;

use Zepluf\Bundle\StoreBundle\Component\Invoice\Invoice as InvoiceComponent;

use Zepluf\Bundle\StoreBundle\Entity\Invoice as InvoiceEntity;

class InvoiceTest extends BaseTestCase
{
    protected $entityManager;
    protected $eventDispatcher;

    protected $invoiceComponent;
    protected $invoiceEntity;

    protected $invoiceItems;

    protected $orderItems;
    protected $fixtures;

    public function setup()
    {
        // $this->entityManager = $this->_container->get('doctrine')->getEntityManager();
        // $this->eventDispatcher = $this->_container->get('event_dispatcher');
    }

    public function tearDown()
    {
    }

    public function testLogic()
    {
        $this->entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $this->eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->invoiceEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Invoice');

        $this->invoiceComponent = new InvoiceComponent($this->entityManager, $this->eventDispatcher);
        $this->invoiceItemCollection = new ArrayCollection();

        // test set entity
        $this->assertSame($this->invoiceComponent, $this->invoiceComponent->setEntity($this->invoiceEntity));

        // test get entity
        $this->assertSame($this->invoiceEntity, $this->invoiceComponent->getEntity());

        // test add invoice items
        $total = 0;
        for ($i = 1; $i <= 10; $i++) {
            $amount = mt_rand(10, 100);
            $quantity = mt_rand(1, 10);

            $total += $amount * $quantity;

            $invoiceItemEntity = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\InvoiceItem');
            $getInventoryItem = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\InventoryItem');
            $getAdjustmentType = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\AdjustmentType');
            $getInvoiceItemType = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\InvoiceItemType');

            $invoiceItemEntity->expects($this->any())
                ->method('getItemDescription')
                ->will($this->returnValue('Invoice Item Description ' . $i));

            $invoiceItemEntity->expects($this->any())
                ->method('getType')
                ->will($this->returnValue($i));

            $invoiceItemEntity->expects($this->any())
                ->method('getQuantity')
                ->will($this->returnValue($quantity));

            $invoiceItemEntity->expects($this->any())
                ->method('getAmount')
                ->will($this->returnValue($amount));

            $invoiceItemEntity->expects($this->any())
                ->method('getIsTaxable')
                ->will($this->returnValue(mt_rand(0, 1)));

            $invoiceItemEntity->expects($this->any())
                ->method('getInventoryItem')
                ->will($this->returnValue($getInventoryItem));

            $invoiceItemEntity->expects($this->any())
                ->method('getAdjustmentType')
                ->will($this->returnValue($getAdjustmentType));

            $invoiceItemEntity->expects($this->any())
                ->method('getInvoiceItemType')
                ->will($this->returnValue($getInvoiceItemType));

            $this->invoiceEntity->expects($this->any())
                ->method('getInvoiceItems')
                ->will($this->returnValue($this->invoiceItemCollection));

            $this->invoiceItemCollection->add($invoiceItemEntity);
        }

        $this->invoiceComponent->setEntity($this->invoiceEntity);

        $this->assertSame($this->invoiceComponent, $this->invoiceComponent->addInvoiceItems($this->invoiceItemCollection));

        // test get total
        $invoiceTotal = $this->invoiceComponent->getTotal();

        $this->assertEquals($total, $invoiceTotal);
        $this->assertGreaterThan($total - 0.0000001, $invoiceTotal);
        $this->assertLessThan($total + 0.0000001, $invoiceTotal);
    }

    function testDatabase()
    {

    }
}
