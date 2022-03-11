<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\ProductVariant\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifySamplePrice(string $price, ChannelInterface $channel): void;

    public function specifyOriginalSamplePrice(string $originalPrice, ChannelInterface $channel): void;

    public function selectSampleShippingCategory(string $shippingCategoryName): void;
}
