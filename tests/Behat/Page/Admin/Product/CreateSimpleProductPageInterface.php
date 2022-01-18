<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPageInterface as BaseCreatePageInterface;

interface CreateSimpleProductPageInterface extends BaseCreatePageInterface
{
    public function enableProductSamples(): void;
}
