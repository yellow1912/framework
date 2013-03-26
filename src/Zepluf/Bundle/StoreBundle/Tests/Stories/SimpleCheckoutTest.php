<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Tests\Stories;

use Doctrine\Common\Collections\ArrayCollection;
use Zepluf\Bundle\StoreBundle\Component\Price\Handler\TaxPriceHandler;

class SimpleCheckoutTest extends \Zepluf\Bundle\StoreBundle\Tests\BaseTestCase
{

    protected $cart;

    public function setUp()
    {
        $this->cart = new \Zepluf\Bundle\StoreBundle\Component\Cart\Cart();
    }

    public function testProductCollection()
    {
    }

    public function testSimpleCheckout()
    {
        // add product to cart
        // $productCollection = $this->getProductCollection();
        // $productCollection->expects($this->once())
        //     ->method('add');

        // save cart

        // create order

        // shipping

        // payment

        // $productCollection = new \Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection();
        // $this->cart->setProductCollection($productCollection);
        // $this->cart->add(1, 1, array(1, 2, 3));
        // $this->cart->setStorageHandler(new \Zepluf\Bundle\StoreBundle\Component\Cart\StorageHandler\SessionStorageHandler());
        // $this->cart->save();

        // create order

        // $order = new \Zepluf\Bundle\StoreBundle\Entity\Order();

        // $order->setType(1);
        // $order->setEntryDate(new \DateTime('11/11/2011'));
        // $order->setOrderDate(new \DateTime('11/11/2011'));

        // $em = $this->get('doctrine')->getManager();

        // $em->persist($order);
        // $em->flush();

        // save order items

        // remove order

        // select shipping

        // create invoice

        // pay
        $orderItems = new ArrayCollection();

        for($i =1; $i <= 5; $i++) {
            $orderItem = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\OrderItem');

            $product = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Product');

            $product->expects($this->once())
                ->method('getId')
                ->will($this->returnValue($i));

            $orderItem->expects($this->once())
                ->method('getProduct')
                ->will($this->returnValue($product));

            $orderItem->expects($this->once())
                ->method('getQuantity')
                ->will($this->returnValue($i));

            $orderItem->expects($this->once())
                ->method('getQuantity')
                ->will($this->returnValue('item ' . $i));

            $orderItem->expects($this->once())
                ->method('getUnitPrice')
                ->will($this->returnValue(10 * $i));

            $orderItems->add($orderItem);
        }
        // add some test items into orderItems

        // get the list of invoice item
        $invoiceItems = new ArrayCollection();
        foreach($orderItems as $orderItem) {

            $orderItem->getProduct()->getId();

            $invoiceItem = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\InvoiceItem');

            $invoiceItem->setQuantity($orderItem->getQuantity());

            $invoiceItem->setAmount($orderItem->getUnitPrice());

            $invoiceItem->setItemDescription($orderItem->getItemDescription());

            $invoiceItem->setIsTaxable(1);

            // $invoiceItem->setTaxable(1);

            $invoiceItems->add($invoiceItem);
        }

        $priceComponent = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\PriceComponent');

        $priceComponent->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(10));

        $taxPriceHandler = new TaxPriceHandler();

        // Value is 10, tax is 10% then the tax amount should be 1
        $this->assertEquals(1, $taxPriceHandler->getPrice(10, $priceComponent));

        // calculate tax

        //
    }

    public function tearDown()
    {
        unset($this->cart);
    }

    protected function getProductCollection()
    {
        return $this->getMock('Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection');
    }
}
