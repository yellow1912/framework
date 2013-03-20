<?php
/**
 * Created by Rubikin Team.
 * Date: 3/20/13
 * Time: 6:13 AM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Inventory;

use Zepluf\Bundle\StoreBundle\Entity\InventoryItem;

class InventoryComponent
{
    protected $doctrine;

    protected $inventoryStrategy;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get the product quantity onhand given the id and the feature combination
     * @param $productId
     * @param array $featureValueIds
     * @return int
     */
    public function getProductQuantity($productId, $featureValueIds = array(), $inventoryItemStatusType = 1)
    {
        $featureValueIds = $this->getFeatureValueIdsString($featureValueIds);

        // get the inventory for the specific set of featureValues
        if (NULL == ($inventory = $this->doctrine->getRepository('StoreBundle:InventoryItem')->findByFeatureValuesIds($productId, $featureValueIds, $inventoryItemStatusType))) {
            return 0;
        } else {
            return $inventory['quantityOnhand'];
        }
    }

    /**
     * Get the product inventory
     *
     * TODO: allow store owners to use different strategy to select
     * the inventory item they want
     */
    public function getProductInventory()
    {

    }

    private function getFeatureValueIdsString($featureValueIds)
    {
        if (is_array($featureValueIds)) {
            // if the features array is not empty, we first sort it
            if (!empty($featureValueIds)) {
                asort($featureValueIds);
            }

            $featureValueIds = implode(',', $featureValueIds);
        }

        return $featureValueIds;
    }
}
