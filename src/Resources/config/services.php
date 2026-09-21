<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BabDev\SyliusProductSamplesPlugin\Checker\SampleAwareProductVariantsParityChecker;
use BabDev\SyliusProductSamplesPlugin\EventListener\SampleVariantGeneratorListener;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\AddToCartTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ChannelTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductGenerateVariantsTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductVariantChoiceTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductVariantTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Form\Type\SampleProductVariantType;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Generator\TemplateSampleVariantCodeGenerator;
use BabDev\SyliusProductSamplesPlugin\Generator\TranslatedPrefixSampleVariantNameGenerator;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductFormMenuBuilder;
use BabDev\SyliusProductSamplesPlugin\Menu\ProductVariantFormMenuBuilder;
use BabDev\SyliusProductSamplesPlugin\Provider\SampleAwareProductVariantPricesProvider;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantOptionValuesSynchronizer;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantOptionValuesSynchronizerInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizer;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizerInterface;
use BabDev\SyliusProductSamplesPlugin\Validator\Constraints\MaxSamplesPerOrderValidator;
use Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder as RootProductFormMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\ProductVariantFormMenuBuilder as RootProductVariantFormMenuBuilder;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductGenerateVariantsType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('babdev_sylius_product_samples.checker.sample_aware_product_variants_parity', SampleAwareProductVariantsParityChecker::class)
        ->decorate('sylius.checker.product_variants_parity')
        ->args([
            service('.inner'),
        ])
    ;

    $services->set('babdev_sylius_product_samples.event_listener.sample_variant_generator', SampleVariantGeneratorListener::class)
        ->args([
            service('sylius.factory.channel_pricing'),
            service('sylius.factory.product_variant'),
            service(SampleVariantCodeGeneratorInterface::class),
            service(ProductVariantOptionValuesSynchronizerInterface::class),
            service(ProductVariantTranslationsSynchronizerInterface::class),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_create', 'method' => 'ensureSampleVariantsExist'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_update', 'method' => 'ensureSampleVariantsExist'])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.add_to_cart', AddToCartTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => AddToCartType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.product_variant_choice', ProductVariantChoiceTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => ProductVariantChoiceType::class])
    ;

    $services->set('babdev_sylius_product_samples.form.extension.product_generate_variants', ProductGenerateVariantsTypeExtension::class)
        ->tag('form.type_extension', ['extended-type' => ProductGenerateVariantsType::class])
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
            service(ProductVariantOptionValuesSynchronizerInterface::class),
            service(ProductVariantTranslationsSynchronizerInterface::class),
            param('sylius.model.product_variant.class'),
            param('sylius.form.type.product_variant.validation_groups'),
        ])
        ->tag('form.type')
    ;

    $services->set('babdev_sylius_product_samples.generator.template_sample_variant_code', TemplateSampleVariantCodeGenerator::class)
        ->args([
            param('babdev_sylius_product_samples.sample_variant_code_template'),
        ])
    ;

    $services->alias(SampleVariantCodeGeneratorInterface::class, 'babdev_sylius_product_samples.generator.template_sample_variant_code');

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

    $services->set('babdev_sylius_product_samples.synchronizer.product_variant.option_values', ProductVariantOptionValuesSynchronizer::class);

    $services->alias(ProductVariantOptionValuesSynchronizerInterface::class, 'babdev_sylius_product_samples.synchronizer.product_variant.option_values');

    $services->set('babdev_sylius_product_samples.synchronizer.product_variant.translations', ProductVariantTranslationsSynchronizer::class)
        ->args([
            service(SampleVariantNameGeneratorInterface::class),
        ])
    ;

    $services->alias(ProductVariantTranslationsSynchronizerInterface::class, 'babdev_sylius_product_samples.synchronizer.product_variant.translations');

    $services->set('babdev_sylius_product_samples.validator.max_samples_per_order', MaxSamplesPerOrderValidator::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.product_variant'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'babdev_sylius_product_samples_max_samples_per_order'])
    ;

    $services->set('babdev_sylius_product_samples.provider.sample_aware_product_variants_prices', SampleAwareProductVariantPricesProvider::class)
        ->decorate('sylius.provider.product_variants_prices')
        ->args([
            service('.inner'),
            service('sylius.calculator.product_variant_price'),
        ])
    ;
};
