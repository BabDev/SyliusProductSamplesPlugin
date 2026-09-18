<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\DependencyInjection;

use BabDev\SyliusProductSamplesPlugin\Generator\TemplateSampleVariantCodeGenerator;
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
                ->scalarNode('sample_variant_code_template')
                    ->defaultValue('SAMPLE-' . TemplateSampleVariantCodeGenerator::CODE_PLACEHOLDER)
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(static fn (string $template): bool => !str_contains($template, TemplateSampleVariantCodeGenerator::CODE_PLACEHOLDER))
                        ->thenInvalid('The sample variant code template must contain the "' . TemplateSampleVariantCodeGenerator::CODE_PLACEHOLDER . '" placeholder, "%s" given.')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
