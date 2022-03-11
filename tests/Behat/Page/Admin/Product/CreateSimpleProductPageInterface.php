<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface CreateSimpleProductPageInterface extends BaseCreatePageInterface
{
    public function enableProductSamples(): void;

    public function specifySamplePrice(ChannelInterface $channel, string $price): void;

    public function specifyOriginalSamplePrice(ChannelInterface $channel, string $originalPrice): void;

    public function selectSampleShippingCategory(string $shippingCategoryName): void;
}
