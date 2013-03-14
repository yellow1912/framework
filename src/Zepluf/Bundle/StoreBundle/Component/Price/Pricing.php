<?php
/**
 * Created by Rubikin Team.
 * Date: 3/13/13
 * Time: 5:18 PM
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zepluf\Bundle\StoreBundle\Component\Price;

class Pricing extends \Symfony\Component\DependencyInjection\ContainerAware
{
    /**
     * an array of handlers
     *
     * @var
     */
    protected $handlers;

    /**
     * add handler
     *
     * @param \Zepluf\Bundle\StoreBundle\Component\Order\PriceHandlerInterface $handler
     */
    public function addHandler(\Zepluf\Bundle\StoreBundle\Component\Price\PriceHandlerInterface $handler)
    {
        $this->handlers[$handler->getCode()] = $handler;
    }

    public function getProductPrice(\Zepluf\Bundle\StoreBundle\Entity\Product $product, $features = array())
    {
        $productPrice = 0;
        // loop through the base price component
        foreach ($product->getPriceComponent() as $priceComponent) {
            $handlerCode = $priceComponent->getHandler();
            if (isset($this->handlers[$handlerCode])) {
                $productPrice = $this->handlers[$handlerCode]->getPrice($productPrice, $priceComponent);
            }
        }

        // get the total of features price
        foreach ($product->getProductFeatureApplication() as $productFeatureApplication) {

            $handlerCode = $priceComponent->getHandler();
            if (isset($this->handlers[$handlerCode])) {
                $productPrice = $this->handlers[$handlerCode]->getPrice($productPrice, $priceComponent);
            }
        }

        // loop through the additional price component

        return $productPrice;
    }

    public function getFeaturePrice()
    {

    }
}
