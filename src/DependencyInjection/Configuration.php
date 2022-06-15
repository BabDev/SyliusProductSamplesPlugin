<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('babdev_sylius_product_samples');

        /** @var ArrayNodeDefinition $root */
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->scalarNode('sample_variant_code_prefix')->defaultValue('SAMPLE-')->cannotBeEmpty()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
