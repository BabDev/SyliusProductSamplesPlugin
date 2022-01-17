<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class BabDevSyliusProductSamplesExtension extends Extension
{
    public function getAlias(): string
    {
        return 'babdev_sylius_product_samples';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
    }
}
