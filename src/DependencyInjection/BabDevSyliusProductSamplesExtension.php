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

    /**
     * @param array<array-key, mixed> $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        /*
         * The API integration depends on services SyliusApiBundle registers, so it is only wired when that
         * bundle is present. An application that does not expose the API is unaffected.
         */
        if ($this->isApiBundleRegistered($container)) {
            $loader->load('services/api.php');
        }

        $container->setParameter('babdev_sylius_product_samples.sample_variant_code_template', $mergedConfig['sample_variant_code_template']);
    }

    private function isApiBundleRegistered(ContainerBuilder $container): bool
    {
        if (!$container->hasParameter('kernel.bundles')) {
            return false;
        }

        $bundles = $container->getParameter('kernel.bundles');

        return \is_array($bundles) && isset($bundles['SyliusApiBundle']);
    }
}
