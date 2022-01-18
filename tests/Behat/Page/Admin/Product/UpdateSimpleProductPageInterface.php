<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface as BaseUpdatePageInterface;

interface UpdateSimpleProductPageInterface extends BaseUpdatePageInterface
{
    public function disableProductSamples(): void;

    public function enableProductSamples(): void;

    public function hasProductSamplesEnabled(): bool;
}
