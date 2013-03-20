<?php
/**
 * Created by Rubikin Team.
 * Date: 3/15/13
 * Time: 11:56 AM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Price;

use Zepluf\Bundle\StoreBundle\Entity\PriceComponent;
use Zepluf\Bundle\StoreBundle\Entity\Product;
/**
 * Basic discount schemes to be implemented here
 */
class ProductDiscountPriceHandler implements PriceHandlerInterface, ProductPriceHandlerInterface
{
    public function getCode()
    {
        return 'product_discount';
    }

    public function getTag()
    {
        return 'product_global';
    }

    public function getPrice($currentPrice, PriceComponent $priceComponent, Product $product)
    {
        return $currentPrice + ($currentPrice * $priceComponent->getValue() / 100);
    }
}
