<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BabDev\SyliusProductSamplesPlugin\Doctrine\QueryCollectionExtension\HideSampleProductVariantsExtension;
use BabDev\SyliusProductSamplesPlugin\Serializer\SampleAwareProductNormalizer;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('babdev_sylius_product_samples.doctrine.query_collection_extension.hide_sample_product_variants', HideSampleProductVariantsExtension::class)
        ->args([
            service(UserContextInterface::class),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services->set('babdev_sylius_product_samples.serializer.sample_aware_product_normalizer', SampleAwareProductNormalizer::class)
        ->args([
            service('api_platform.symfony.iri_converter'),
            service(UserContextInterface::class),
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;
};
