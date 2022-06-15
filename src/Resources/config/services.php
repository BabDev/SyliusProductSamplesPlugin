<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BabDev\SyliusProductSamplesPlugin\EventListener\SampleVariantGeneratorListener;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ChannelTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductVariantTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Type\SampleProductVariantType;
use BabDev\SyliusProductSamplesPlugin\Generator\ChannelAwareSampleVariantCodeGenerator;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Generator\StaticPrefixSampleVariantCodeGenerator;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductFormMenuBuilder;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductVariantFormMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder as RootProductFormMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ProductVariantFormMenuBuilder as RootProductVariantFormMenuBuilder;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    // TODO - Decide if we really need this listener, we should be able to handle this entirely within the forms
    $services->set('babdev_sylius_product_samples.event_listener.sample_variant_generator', SampleVariantGeneratorListener::class)
        ->args([
            service('sylius.factory.channel_pricing'),
            service('sylius.factory.product_variant'),
            service(SampleVariantCodeGeneratorInterface::class),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_create', 'method' => 'ensureSampleVariantsExist'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_update', 'method' => 'ensureSampleVariantsExist'])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.channel', ChannelTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => ChannelType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.product', ProductTypeExtension::class)
        ->args([
            service(SampleVariantCodeGeneratorInterface::class),
        ])
        ->tag('form.type_extension', ['extended-type' => ProductType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.product_variant', ProductVariantTypeExtension::class)
        ->args([
            service('sylius.factory.product_variant'),
            service(SampleVariantCodeGeneratorInterface::class),
        ])
        ->tag('form.type_extension', ['extended-type' => ProductVariantType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.type.sample_product_variant', SampleProductVariantType::class)
        ->args([
            param('sylius.model.product_variant.class'),
            param('sylius.form.type.product_variant.validation_groups'),
        ])
        ->tag('form.type')
    ;

    $services->set('babdev_sylius_product_samples.generator.static_prefix_sample_variant_code', StaticPrefixSampleVariantCodeGenerator::class)
    ;

    $services->set('babdev_sylius_product_samples.generator.channel_aware_sample_variant_code', ChannelAwareSampleVariantCodeGenerator::class)
        ->decorate('babdev_sylius_product_samples.generator.static_prefix_sample_variant_code')
        ->args([
            service('sylius.context.channel'),
            service('.inner')
        ])
    ;

    $services->alias(SampleVariantCodeGeneratorInterface::class, 'babdev_sylius_product_samples.generator.channel_aware_sample_variant_code');

    $services->set('babdev_sylius_product_samples.menu.admin.product.form', ProductFormMenuBuilder::class)
        ->tag('kernel.event_listener', ['event' => RootProductFormMenuBuilder::EVENT_NAME, 'method' => 'addProductSamplesMenu'])
    ;

    $services->set('babdev_sylius_product_samples.menu.admin.product_variant.form', ProductVariantFormMenuBuilder::class)
        ->tag('kernel.event_listener', ['event' => RootProductVariantFormMenuBuilder::EVENT_NAME, 'method' => 'addProductSamplesMenu'])
    ;
};
