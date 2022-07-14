<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Product;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface as BaseShowPageInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;

interface ShowPageInterface extends BaseShowPageInterface
{
    /**
     * @throws ElementNotFoundException
     */
    public function requestASampleWithOption(ProductOptionInterface $option, string $optionValue): void;
}
