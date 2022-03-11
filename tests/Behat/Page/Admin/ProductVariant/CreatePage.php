<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\ProductVariant\CreatePage as BaseCreatePage;
use Sylius\Component\Core\Model\ChannelInterface;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function specifySamplePrice(string $price, ChannelInterface $channel): void
    {
        $this->getElement('sample_price', ['%channelCode%' => $channel->getCode()])->setValue($price);
    }

    public function specifyOriginalSamplePrice(string $originalPrice, ChannelInterface $channel): void
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
            'sample_price' => '#sylius_product_variant_sample_channelPricings input[id*="%channelCode%"]',
            'sample_original_price' => '#sylius_product_variant_sample_channelPricings input[name$="[originalPrice]"][id*="%channelCode%"]',
            'sample_shipping_category' => '#sylius_product_variant_sample_shippingCategory',
        ]);
    }
}
