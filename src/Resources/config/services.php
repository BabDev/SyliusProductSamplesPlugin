<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BabDev\SyliusProductSamplesPlugin\EventListener\SampleVariantGeneratorListener;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\AddToCartTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\CartItemTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\CartTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ChannelTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductVariantTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Type\SampleProductVariantType;
use BabDev\SyliusProductSamplesPlugin\Generator\ChannelAwareSampleVariantCodeGenerator;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Generator\StaticPrefixSampleVariantCodeGenerator;
use BabDev\SyliusProductSamplesPlugin\Generator\TranslatedPrefixSampleVariantNameGenerator;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductFormMenuBuilder;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductVariantFormMenuBuilder;
use BabDev\SyliusProductSamplesPlugin\Provider\SampleAwareProductVariantPricesProvider;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizer;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizerInterface;
use Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder as RootProductFormMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ProductVariantFormMenuBuilder as RootProductVariantFormMenuBuilder;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\OrderBundle\Form\Type\CartType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('babdev_sylius_product_samples.event_listener.sample_variant_generator', SampleVariantGeneratorListener::class)
        ->args([
            service('sylius.factory.channel_pricing'),
            service('sylius.factory.product_variant'),
            service(SampleVariantCodeGeneratorInterface::class),
            service(ProductVariantTranslationsSynchronizerInterface::class),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_create', 'method' => 'ensureSampleVariantsExist'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_update', 'method' => 'ensureSampleVariantsExist'])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.add_to_cart', AddToCartTypeExtension::class)
        ->args([
            service('sylius.context.channel'),
        ])
        ->tag('form.type_extension', ['extended-type' => AddToCartType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.cart', CartTypeExtension::class)
        ->args([
            service('sylius.context.channel'),
            service('translator'),
        ])
        ->tag('form.type_extension', ['extended-type' => CartType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.cart_item_type', CartItemTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => CartItemType::class, 'priority' => -10]) // Needs to run after the extension in SyliusCoreBundle
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
            service(ProductVariantTranslationsSynchronizerInterface::class),
            param('sylius.model.product_variant.class'),
            param('sylius.form.type.product_variant.validation_groups'),
        ])
        ->tag('form.type')
    ;

    $services->set('babdev_sylius_product_samples.generator.static_prefix_sample_variant_code', StaticPrefixSampleVariantCodeGenerator::class)
        ->args([
            param('babdev_sylius_product_samples.sample_variant_code_prefix'),
        ])
    ;

    $services->set('babdev_sylius_product_samples.generator.channel_aware_sample_variant_code', ChannelAwareSampleVariantCodeGenerator::class)
        ->decorate('babdev_sylius_product_samples.generator.static_prefix_sample_variant_code')
        ->args([
            service('sylius.context.channel'),
            service('.inner'),
        ])
    ;

    $services->alias(SampleVariantCodeGeneratorInterface::class, 'babdev_sylius_product_samples.generator.channel_aware_sample_variant_code');

    $services->set('babdev_sylius_product_samples.generator.translated_prefix_sample_variant_name', TranslatedPrefixSampleVariantNameGenerator::class)
        ->args([
            service('translator'),
        ])
    ;

    $services->alias(SampleVariantNameGeneratorInterface::class, 'babdev_sylius_product_samples.generator.translated_prefix_sample_variant_name');

    $services->set('babdev_sylius_product_samples.menu.admin.product.form', ProductFormMenuBuilder::class)
        ->tag('kernel.event_listener', ['event' => RootProductFormMenuBuilder::EVENT_NAME, 'method' => 'addProductSamplesMenu'])
    ;

    $services->set('babdev_sylius_product_samples.menu.admin.product_variant.form', ProductVariantFormMenuBuilder::class)
        ->tag('kernel.event_listener', ['event' => RootProductVariantFormMenuBuilder::EVENT_NAME, 'method' => 'addProductSamplesMenu'])
    ;

    $services->set('babdev_sylius_product_samples.synchronizer.product_variant.translations', ProductVariantTranslationsSynchronizer::class)
        ->args([
            service(SampleVariantNameGeneratorInterface::class),
        ])
    ;

    $services->alias(ProductVariantTranslationsSynchronizerInterface::class, 'babdev_sylius_product_samples.synchronizer.product_variant.translations');

    /*
     * The below services fully replace Sylius core services
     */

    $services->set('sylius.provider.product_variants_prices', SampleAwareProductVariantPricesProvider::class)
        ->args([
            service('sylius.calculator.product_variant_price'),
        ])
    ;
};
