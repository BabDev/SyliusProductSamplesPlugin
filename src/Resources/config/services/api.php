<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BabDev\SyliusProductSamplesPlugin\Doctrine\QueryCollectionExtension\HideSampleProductVariantsExtension;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('babdev_sylius_product_samples.doctrine.query_collection_extension.hide_sample_product_variants', HideSampleProductVariantsExtension::class)
        ->args([
            service(UserContextInterface::class),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;
};
