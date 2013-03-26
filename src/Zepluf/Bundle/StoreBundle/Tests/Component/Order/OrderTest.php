<?php
/**
 * Created by Rubikin Team.
 * Date: 3/22/13
 * Time: 3:31 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Tests\Component\Order;


use Zepluf\Bundle\StoreBundle\Component\Order\Order;
use Zepluf\Bundle\StoreBundle\Component\Price\Pricing;
use Zepluf\Bundle\StoreBundle\Exceptions\ProductException;
use Zepluf\Bundle\StoreBundle\Tests\BaseTestCase;

class OrderTest extends BaseTestCase
{

    public function testCreate()
    {
//        $productCollection = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection');
//
//        $productCollection->expects($this->once())
//            ->method('getAll')
//            ->will($this->returnValue(array(
//                'key1' => array(
//                    'productId' => 1,
//                    'quantity' => 2,
//                    'features' => array()
//                ),
//                'key1' => array(
//                    'productId' => 2,
//                    'quantity' => 2,
//                    'features' => array()
//                ),
//            )));
//
//        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
//
//
//        $product = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Product');
//
//        $product->expects($this->once())
//            ->method('getType')
//            ->will($this->returnValue(1));
//
//        $product->expects($this->once())
//            ->method('getName')
//            ->will($this->returnValue('This is a product'));
//
//        $entityManager->expects($this->any())
//            ->method('find')
//            ->will($this->returnValue($product));
//
//        $price = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Price\Price');
//        $price->expects($this->any())
//            ->method('getTotal')
//            ->will($this->returnValue(10));
//
//        $pricing = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Price\Pricing');
//        $pricing->expects($this->any())
//            ->method('getProductPrice')
//            ->will($this->returnValue($price));
//
//        $orderComponent = new Order($entityManager, $pricing);
//
//        $orderComponent->create($productCollection);

    }

    public function testAddOrderItems()
    {
        // test add not exist product
        $productCollection = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection');

        $productCollection->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue(array(
                'key1' => array(
                    'productId' => -9999,
                    'quantity' => 2,
                    'features' => array()
                )
            )));

        $product = $this->getMock('Zepluf\Bundle\StoreBundle\Entity\Product');

        $product->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(1));

        $product->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('This is a product'));

        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $entityManager->expects($this->any())
            ->method('find')
            ->will($this->returnValue($product));

        $price = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Price\Price');
        $price->expects($this->any())
            ->method('getTotal')
            ->will($this->returnValue(10));

        $pricing = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Price\Pricing');
        $pricing->expects($this->any())
            ->method('getProductPrice')
            ->will($this->returnValue($price));

        $orderComponent = new Order($entityManager, $pricing);
        
        try {
            $orderComponent->addOrderItems($productCollection);
        } catch (ProductException $pe) {
            $this->assertEquals(ProductException::NOT_FOUND, $pe->getCode());
            return;
        }

        $this->fail('An expected exception has not been raised.');

    }
}