<?php
/**
 * Created by Rubikin Team.
 * Date: 3/4/13
 * Time: 5:41 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Pricing\Handler;

use Zepluf\Bundle\StoreBundle\Entity\PriceEntity;

class DefaultPriceHandler implements PriceHandlerInterface
{
    public function getCode()
    {
        return 'product_default';
    }

    public function getTag()
    {
        return 'product';
    }

    public function getPrice($currentPrice, PriceEntity $priceComponent)
    {
        return $priceComponent->getValue();
    }
}
