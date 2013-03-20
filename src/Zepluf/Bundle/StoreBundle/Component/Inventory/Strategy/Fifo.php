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
use Zepluf\Bundle\StoreBundle\Entity\Product;

/**
 * Class DefaultStrategy
 *
 * The default strategy will pick a random inventory
 *
 * @package Zepluf\Bundle\StoreBundle\Component\Inventory\Strategy
 */
class DefaultStrategy implements InventoryStrategyInterface
{
    public function getInventories(EntityManager $entityManager, Product $product, $featureValueIds, $quantity)
    {

    }
}