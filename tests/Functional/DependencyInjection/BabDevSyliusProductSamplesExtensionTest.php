<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Functional\DependencyInjection;

use BabDev\SyliusProductSamplesPlugin\DependencyInjection\BabDevSyliusProductSamplesExtension;
use BabDev\SyliusProductSamplesPlugin\Validator\Constraints\MaxSamplesPerOrder;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class BabDevSyliusProductSamplesExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function the_container_is_loaded_with_the_plugin_services(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('babdev_sylius_product_samples.menu.admin.product.form');
    }

    /**
     * @test
     */
    public function the_max_samples_per_order_validator_is_registered_with_the_alias_the_constraint_resolves(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'babdev_sylius_product_samples.validator.max_samples_per_order',
            'validator.constraint_validator',
            ['alias' => (new MaxSamplesPerOrder())->validatedBy()],
        );
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [
            new BabDevSyliusProductSamplesExtension(),
        ];
    }
}
