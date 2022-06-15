<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class BabDevSyliusProductSamplesExtension extends ConfigurableExtension
{
    public function getAlias(): string
    {
        return 'babdev_sylius_product_samples';
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $container->setParameter('babdev_sylius_product_samples.sample_variant_code_prefix', $mergedConfig['sample_variant_code_prefix']);
    }
}
