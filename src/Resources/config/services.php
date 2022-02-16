<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BabDev\SyliusProductSamplesPlugin\Form\Extension\ChannelTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductFormMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder as RootProductFormMenuBuilder;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('babdev_sylius_product_samples.form.extension.channel', ChannelTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => ChannelType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.product', ProductTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => ProductType::class])
    ;

    $services->set('babdev_sylius_product_samples.menu.admin.product.form', ProductFormMenuBuilder::class)
        ->tag('kernel.event_listener', ['event' => RootProductFormMenuBuilder::EVENT_NAME, 'method' => 'addProductSamplesMenu'])
    ;
};
