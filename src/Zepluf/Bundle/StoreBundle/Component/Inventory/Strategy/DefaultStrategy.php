<?php
/**
 * Created by Rubikin Team.
 * Date: 3/20/13
 * Time: 1:37 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Inventory\Strategy;


use Doctrine\ORM\EntityManager;

/**
 * Class DefaultStrategy
 *
 * The default strategy will pick a random inventory
 *
 * @package Zepluf\Bundle\StoreBundle\Component\Inventory\Strategy
 */
class DefaultStrategy implements InventoryStrategyInterface
{

    /**
     * {@inheritdoc}
     */
    public function getInventories(EntityManager $entityManager, $productId, $featureValueIds, $quantity, $inventoryItemStatusType = 1)
    {
        $inventoryStack = array();

        // attempt to get a single inventory that can satisfies the quantity
        $inventory = $entityManager->createQuery(
            'SELECT i FROM Zepluf\Bundle\StoreBundle\Entity\InventoryItem i
             WHERE i.product = :productId AND i.featureValueIds = :featureValueIds AND i.inventoryItemStatusType = :inventoryItemStatusType AND i.quantityOnhand > = :quantityOnhand
             ')
            ->setParameters(array('productId' => $productId, 'inventoryItemStatusType' => $inventoryItemStatusType, 'quantityOnhand' => $quantity, 'featureValueIds' => $featureValueIds))->getOneOrNullResult();

        // failed? Then we will have to get multiple inventories
        if (NULL == $inventory) {
            $stackQuantity = 0;
            $inventories = $entityManager->createQuery(
                'SELECT i FROM Zepluf\Bundle\StoreBundle\Entity\InventoryItem i
                 WHERE i.product = :productId AND i.featureValueIds = :featureValueIds AND i.inventoryItemStatusType = :inventoryItemStatusType ORDER BY i.quantityOnhand DESC
                 ')
                ->setParameters(array('productId' => $productId, 'inventoryItemStatusType' => $inventoryItemStatusType, 'featureValueIds' => $featureValueIds))->getResult();

            foreach ($inventories as $inventory) {
                $stackQuantity += $inventory->getQuantityOnhand();
                $inventoryStack[] = $inventory;
                if ($stackQuantity >= $quantity) {
                    break;
                }
            }
        } else {
            $inventoryStack[] = $inventory;
        }

        return $inventoryStack;
    }
}