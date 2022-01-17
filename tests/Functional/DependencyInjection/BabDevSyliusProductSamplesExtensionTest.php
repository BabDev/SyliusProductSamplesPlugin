<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Functional\DependencyInjection;

use BabDev\SyliusProductSamplesPlugin\DependencyInjection\BabDevSyliusProductSamplesExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

final class BabDevSyliusProductSamplesExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function the_container_is_loaded_with_the_plugin_services(): void
    {
        $this->load();
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
