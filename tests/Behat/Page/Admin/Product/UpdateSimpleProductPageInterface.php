<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface UpdateSimpleProductPageInterface extends BaseUpdatePageInterface
{
    public function disableProductSamples(): void;

    public function enableProductSamples(): void;

    public function hasProductSamplesEnabled(): bool;

    public function specifySamplePrice(ChannelInterface $channel, string $price): void;

    public function specifyOriginalSamplePrice(ChannelInterface $channel, string $originalPrice): void;

    public function getSamplePriceForChannel(ChannelInterface $channel): string;

    public function getOriginalSamplePriceForChannel(ChannelInterface $channel): string;
}
