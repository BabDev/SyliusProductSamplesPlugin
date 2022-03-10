<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPage as BaseUpdatePage;
use Sylius\Component\Core\Model\ChannelInterface;

class UpdateSimpleProductPage extends BaseUpdatePage implements UpdateSimpleProductPageInterface
{
    public function disableProductSamples(): void
    {
        $this->getElement('samples_active')->uncheck();
    }

    public function enableProductSamples(): void
    {
        $this->getElement('samples_active')->check();
    }

    public function hasProductSamplesEnabled(): bool
    {
        return $this->getElement('samples_active')->isChecked();
    }

    public function specifySamplePrice(ChannelInterface $channel, string $price): void
    {
        $this->getElement('sample_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalSamplePrice(ChannelInterface $channel, string $originalPrice): void
    {
        $this->getElement('sample_original_price', ['%channelCode%' => $channel->getCode()])->setValue($originalPrice);
    }

    public function getSamplePriceForChannel(ChannelInterface $channel): string
    {
        return $this->getElement('sample_price', ['%channelCode%' => $channel->getCode()])->getValue();
    }

    public function getOriginalSamplePriceForChannel(ChannelInterface $channel): string
    {
        return $this->getElement('sample_original_price', ['%channelCode%' => $channel->getCode()])->getValue();
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
