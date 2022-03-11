<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function getSamplePriceForChannel(ChannelInterface $channel): string;

    public function getOriginalSamplePriceForChannel(ChannelInterface $channel): string;
}
