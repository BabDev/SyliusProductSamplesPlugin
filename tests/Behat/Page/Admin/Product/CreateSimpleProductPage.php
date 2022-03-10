<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPage as BaseCreatePage;
use Sylius\Component\Core\Model\ChannelInterface;

class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    public function enableProductSamples(): void
    {
        $this->getElement('samples_active')->check();
    }

    public function specifySamplePrice(ChannelInterface $channel, string $price): void
    {
        $this->getElement('sample_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalSamplePrice(ChannelInterface $channel, int $originalPrice): void
    {
        $this->getElement('sample_original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function selectSampleShippingCategory(string $shippingCategoryName): void
    {
        $this->getElement('sample_shipping_category')->selectOption($shippingCategoryName);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'samples_active' => '#sylius_product_samplesActive',
            'sample_price' => '#sylius_product_variant_sample_channelPricings input[id*="%channelCode%"]',
            'sample_original_price' => '#sylius_product_variant_sample_channelPricings input[name$="[originalPrice]"][id*="%channelCode%"]',
            'sample_shipping_category' => '#sylius_product_variant_sample_shippingCategory',
        ]);
    }
}
