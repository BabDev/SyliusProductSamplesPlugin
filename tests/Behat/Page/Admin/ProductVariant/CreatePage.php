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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'sample_price' => '#sylius_product_variant_sample_channelPricings input[id*="%channelCode%"]',
        ]);
    }
}
