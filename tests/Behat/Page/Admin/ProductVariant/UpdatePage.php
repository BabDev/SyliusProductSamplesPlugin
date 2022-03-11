<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\ProductVariant\UpdatePage as BaseUpdatePage;
use Sylius\Component\Core\Model\ChannelInterface;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
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
            'sample_price' => '#sylius_product_variant_sample_channelPricings input[id*="%channelCode%"]',
            'sample_original_price' => '#sylius_product_variant_sample_channelPricings input[name$="[originalPrice]"][id*="%channelCode%"]',
            'sample_shipping_category' => '#sylius_product_variant_sample_shippingCategory',
        ]);
    }
}
