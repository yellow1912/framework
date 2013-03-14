<?php
namespace Zepluf\Bundle\StoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


class StorePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
<<<<<<< HEAD
=======
        if (false !== ($definition = $container->getDefinition('storebundle.payment_methods'))) {
            if (false !== ($taggedServices = $container->findTaggedServiceIds('storebundle.payment_methods.method'))) {
                foreach ($taggedServices as $id => $tagAttributes) {
                    foreach ($tagAttributes as $attributes) {
                        $definition->addMethodCall('addMethod',
                            array(new Reference($id), $attributes["alias"])
                        );
                    }
                }
            }
        }


>>>>>>> 9cda9ba8825f6f9dbff13b0b3726e1d596b3262c
        if (!$container->hasDefinition('storebundle.shipment')) {
            return;
        }

        $definition = $container->getDefinition(
            'storebundle.shipment'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'storebundle.shipment.carrier'
        );
<<<<<<< HEAD
=======

>>>>>>> 9cda9ba8825f6f9dbff13b0b3726e1d596b3262c
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addCarriers',
                    array(new Reference($id), $attributes["alias"])
                );
            }
        }
    }
}