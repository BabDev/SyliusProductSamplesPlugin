<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BabDev\SyliusProductSamplesPlugin\EventListener\ProductListener;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ChannelTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductVariantTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Type\SampleProductVariantType;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductFormMenuBuilder;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductVariantFormMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder as RootProductFormMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ProductVariantFormMenuBuilder as RootProductVariantFormMenuBuilder;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('babdev_sylius_product_samples.event_listener.product', ProductListener::class)
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_create', 'method' => 'ensureSampleVariantsHaveCodes'])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.channel', ChannelTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => ChannelType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.product', ProductTypeExtension::class)
        ->args([
            service('sylius.factory.product_variant'),
        ])
        ->tag('form.type_extension', ['extended-type' => ProductType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.product_variant', ProductVariantTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => ProductVariantType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.type.sample_product_variant', SampleProductVariantType::class)
        ->args([
            param('sylius.model.product_variant.class'),
            param('sylius.form.type.product_variant.validation_groups'),
        ])
        ->tag('form.type')
    ;

    $services->set('babdev_sylius_product_samples.menu.admin.product.form', ProductFormMenuBuilder::class)
        ->tag('kernel.event_listener', ['event' => RootProductFormMenuBuilder::EVENT_NAME, 'method' => 'addProductSamplesMenu'])
    ;

    $services->set('babdev_sylius_product_samples.menu.admin.product_variant.form', ProductVariantFormMenuBuilder::class)
        ->tag('kernel.event_listener', ['event' => RootProductVariantFormMenuBuilder::EVENT_NAME, 'method' => 'addProductSamplesMenu'])
    ;
};
