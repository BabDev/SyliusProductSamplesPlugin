<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin;

use BabDev\SyliusProductSamplesPlugin\DependencyInjection\BabDevSyliusProductSamplesExtension;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BabDevSyliusProductSamplesPlugin extends Bundle
{
    use SyliusPluginTrait;

    protected function getBundlePrefix(): string
    {
        return 'babdev_sylius_product_samples';
    }

    /**
     * @psalm-suppress DocblockTypeContradiction
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->containerExtension) {
            $this->containerExtension = new BabDevSyliusProductSamplesExtension();
        }

        /**
         * @phpstan-ignore-next-line
         */
        return $this->containerExtension === false ? null : $this->containerExtension;
    }
}
