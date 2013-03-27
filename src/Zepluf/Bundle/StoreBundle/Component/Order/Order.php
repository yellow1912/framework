<?php
/**
 * Created by Rubikin Team.
 * Date: 3/7/13
 * Time: 7:00 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Order;

use Zepluf\Bundle\StoreBundle\Component\Product\ProductCollection;
use Zepluf\Bundle\StoreBundle\Entity\Order as OrderEntity;
use Doctrine\ORM\EntityManager;
use Zepluf\Bundle\StoreBundle\Entity\OrderItem;
use Zepluf\Bundle\StoreBundle\Component\Price\Pricing;
use Zepluf\Bundle\StoreBundle\Exceptions\ProductException;

class Order
{
    protected $entityManager;


    /**
     * @var \Zepluf\Bundle\StoreBundle\Entity\Order
     */
    protected $order;

    protected $pricing;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, Pricing $pricing)
    {
        $this->entityManager = $entityManager;
        $this->pricing = $pricing;
    }

    public function retrieve()
    {

    }

    /**
     * Set order
     *
     * @param \Zepluf\Bundle\StoreBundle\Entity\Order $order
     */

    public function setEntity($order)
    {
        $this->order = $order;
    }

    public function getEntity()
    {
        return $this->order;
    }

    /**
     * @param ProductCollection $productCollection
     * @param $type
     */
    public function create(ProductCollection $productCollection, $type = OrderType::ORDER_TYPE)
    {
        $this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $this->order = new OrderEntity();

            // sets the order type
            $this->order->setType($type);

            // set the order timestamp
            $this->order->setOrderDate(new \DateTime("now"));

            // set the entry timestamp
            $this->order->setEntryDate(new \DateTime("now"));

            // insert new order item
            $this->addOrderItems($productCollection);

            // persists the order
            $this->entityManager->persist($this->order);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        }
        catch (\Exception $e) {
            $this->entityManager->getConnection()->rollback();
            $this->entityManager->close();
            throw $e;
        }

    }

    /**
     * Add items into order
     *
     * @param ProductCollection $productCollection
     */
    public function addOrderItems(ProductCollection $productCollection)
    {
        // TODO: we can use 1 single query to get the info we want to optimize performance
        if (false !== ($products = $productCollection->getAll())) {
            foreach ($products as $key => $product) {
                $orderItem = new OrderItem();

                $productEntity = $this->entityManager->find('StoreBundle:Product', $product['productId']);
                if( NULL == $productEntity->getId()) {
                    throw new ProductException(sprintf('Product with id %s not found', $product['productId']), ProductException::NOT_FOUND);
                }

                // set price
                $orderItem->setUnitPrice($this->pricing->getProductPrice($productEntity, $product['features'])->getTotal());

                $orderItem->setType($productEntity->getType());

                $orderItem->setProduct($productEntity);

                // set quantity
                $orderItem->setQuantity($product['quantity']);

                // set name (description)
                $orderItem->setItemDescription($productEntity->getName());

                // set order
                $orderItem->setOrder($this->order);
            }
        }

        $this->order->addOrderItem($orderItem);
    }

    public function addInvoice()
    {

    }
}
