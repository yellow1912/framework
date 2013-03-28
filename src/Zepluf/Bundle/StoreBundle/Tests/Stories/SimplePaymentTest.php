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

use Zepluf\Bundle\StoreBundle\Component\Invoice\Invoice;

class SimplePaymentTest extends \Zepluf\Bundle\StoreBundle\Tests\BaseTestCase
{
    protected $invoice;

    protected $orderItemCollection;

    public function setUp()
    {
        // $this->invoice = new Invoice($this->_container->get('doctrine')->getEntityManager(), $this->_container->get('event_dispatcher'));

        // $this->orderItemCollection = $this->getMock('Doctrine\Common\Collections\ArrayCollection');
    }

    public function testProductCollection()
    {
    }

    public function testSimplePayment()
    {
        // if (method_exists($this->orderItemCollection, 'isEmpty')) {
        //     echo 'true';
        // } else {
        //     echo 'false';
        // }
        // exit();
        // $this->assertTrue(false, $this->invoice->create($this->orderItemCollection));

        // echo 'jkMedia';

        // add product to cart
        // $productCollection = $this->getProductCollection();
        // $productCollection->expects($this->once())->method('add');

        // save cart

        // create order

        // shipping

        // payment

//        $productCollection = new \Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection();
//        $this->cart->setProductCollection($productCollection);
//        $this->cart->add(1, 1, array(1, 2, 3));
//        $this->cart->setStorageHandler(new \Zepluf\Bundle\StoreBundle\Component\Cart\StorageHandler\SessionStorageHandler());
//        $this->cart->save();

        // create order

//        $order = new \Zepluf\Bundle\StoreBundle\Entity\Order();
//
//        $order->setType(1);
//        $order->setEntryDate(new \DateTime('11/11/2011'));
//        $order->setOrderDate(new \DateTime('11/11/2011'));
//
//        $em = $this->get('doctrine')->getManager();
//
//        $em->persist($order);
//        $em->flush();

        // save order items

        // remove order

        // select shipping

        // create invoice

        // pay
    }

    public function tearDown()
    {
        // unset($this->cart);
    }
}
