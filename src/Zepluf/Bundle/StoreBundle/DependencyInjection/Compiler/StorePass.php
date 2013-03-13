<?php
namespace Zepluf\Bundle\StoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


class StorePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('storebundle.payment')) {
            return;
        }

        $definition = $container->getDefinition(
            'storebundle.payment'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'storebundle.payment.payment_method'
        );
        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addPaymentMethod',
                    array(new Reference($id), $attributes["alias"])
                );
            }
        }


        if (!$container->hasDefinition('storebundle.shipment')) {
            return;
        }

        $definition = $container->getDefinition(
            'storebundle.shipment'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'storebundle.shipment.carrier'
        );

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