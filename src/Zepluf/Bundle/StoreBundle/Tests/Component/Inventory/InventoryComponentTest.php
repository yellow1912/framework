<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Tests\Component\Inventory;

class InventoryComponentTest extends \Zepluf\Bundle\StoreBundle\Tests\BaseTestCase
{
    public function testGetProductQuantity()
    {
        // TODO: setup the fixtures to test?
        $inventoryComponent = new \Zepluf\Bundle\StoreBundle\Component\Inventory\InventoryComponent($this->_container->get('doctrine'));
        $this->assertEquals(0, $inventoryComponent->getProductQuantity(1, array(1,2,3)));
    }
}
