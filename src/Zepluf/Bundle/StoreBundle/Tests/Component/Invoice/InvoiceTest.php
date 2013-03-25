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

use Zepluf\Bundle\StoreBundle\Component\Payment\Fixtures;
use Zepluf\Bundle\StoreBundle\Component\Invoice\Invoice as InvoiceComponent;

class InvoiceTest extends BaseTestCase
{
    protected $invoice;
    protected $orderItems;
    protected $fixtures;

    public function setup()
    {
        $this->fixtures = new Fixtures($this->_container->get('doctrine'));
        $this->fixtures->setup();

        $this->invoice = new InvoiceComponent($this->_container->get('doctrine')->getEntityManager(), $this->_container->get('event_dispatcher'));

        $this->orderItems = $this->getMock('\Doctrine\Common\Collections\ArrayCollection');

        $orderItem = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\OrderItem');

        $orderItem->expects($this->once())
            ->method('getItemDescription')
            ->will($this->returnValue('Order Item Description'));

        $orderItem->expects($this->once())
            ->method('getQuantity')
            ->will($this->returnValue(1));

        $this->orderItems->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(array($orderItem)));
    }

    public function tearDown()
    {
        $this->fixtures->tearDown();
        $this->fixtures->tearDown(array('invoice', 'invoice_item'));
    }

    public function testCreateInvoice()
    {
        $invoiceData = array(
            'billedTo' => 1,
            'billedFrom' => 1,
            'addressedTo' => 1,
            'sendTo' => 1,

            'message' => 'Data message',
            'description' => 'Data description',
            'orderItems' => $this->orderItems
        );

        $this->assertTrue($this->invoice->create($invoiceData));
    }
}
