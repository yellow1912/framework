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


use Doctrine\DBAL\DBALException;
use Zepluf\Bundle\StoreBundle\Component\Order\Order;
use Zepluf\Bundle\StoreBundle\Component\Price\Pricing;
use Zepluf\Bundle\StoreBundle\Exceptions\ProductException;
use Zepluf\Bundle\StoreBundle\Tests\BaseTestCase;

class OrderTest extends BaseTestCase
{

    private $productCollection;

    private $product;

    private $price;

    private $pricing;

    public function setUp()
    {
        // test add not exist product
        $this->productCollection = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection');

        $this->productCollection->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue(array(
                'key1' => array(
                    'productId' => 1,
                    'quantity' => 1,
                    'features' => array()
                ),
                'key2' => array(
                    'productId' => 2,
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


        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $this->entityManager->expects($this->any())
            ->method('find')
            ->will($this->returnValue($product));

        $this->price = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Price\Price');
        $this->price->expects($this->any())
            ->method('getTotal')
            ->will($this->returnValue(10));

        $this->pricing = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Price\Pricing');
        $this->pricing->expects($this->any())
            ->method('getProductPrice')
            ->will($this->returnValue($this->price));
    }

    public function testCreate()
    {

        $em = $this->_container->get('doctrine.orm.entity_manager');

        $loader = new \Nelmio\Alice\Loader\Yaml();

        $objects = $loader->load(__DIR__ . '/../../../Resources/fixtures/product.yml', $em);
        $persister = new \Nelmio\Alice\ORM\Doctrine($em);
        $persister->persist($objects);

        $orderComponent = new Order($em, $this->pricing);

        $productCollection = $this->getMock('Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection');

        //Get the first $amount users starting from a random point
        $query = $em->createQuery('
                SELECT DISTINCT p
                FROM StoreBundle:Product p ORDER BY p.id DESC')
            ->setMaxResults(4);

        $result = $query->getResult();

        $productCollectionData = array();
        foreach ($result as $product) {
            $productCollectionData[$product->getId()] = array(
                'productId' => $product->getId(),
                'quantity' => 1,
                'features' => array()
            );
        }

        $productCollection->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue($productCollectionData));

        try {
            $orderComponent->create($productCollection);
        } catch (DBALException $pe) {
            $this->fail('Unexpected exception ' . $pe->getMessage());
            return;
        }

        foreach ($result as $object) {
            $em->refresh($object);
            $em->remove($object);
        }

        $em->flush();

        $em->remove($orderComponent->getEntity());

        $em->flush();
    }

    public function testAddNotExistOrderItems()
    {
        $this->productCollection->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue(array(
                'key1' => array(
                    'productId' => -9999,
                    'quantity' => 1,
                    'features' => array()
                ),
                'key2' => array(
                    'productId' => 2,
                    'quantity' => 2,
                    'features' => array()
                )
            )));

        // test add not exist product
        $orderComponent = new Order($this->entityManager, $this->pricing);

        try {
            $orderComponent->addOrderItems($this->productCollection);
        } catch (ProductException $pe) {
            $this->assertEquals(ProductException::NOT_FOUND, $pe->getCode());
            return;
        }

        $this->fail('An expected exception has not been raised.');

    }
}