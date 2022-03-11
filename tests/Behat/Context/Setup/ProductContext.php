<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Setup;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    public function __construct(
        private ObjectManager $objectManager,
        private FactoryInterface $channelPricingFactory,
        private ProductVariantFactoryInterface $productVariantFactory,
    ) {
    }

    /**
     * @Given /^(this product) has product samples enabled for all channels$/
     * @Given /^the ("([^"]*)" product) has product samples enabled for all channels$/
     */
    public function theProductHasSamplesEnabledForAllChannels(ProductInterface $product): void
    {
        $this->enableSamplesForAllChannelsWithPrice($product, 0);
    }

    /**
     * @Given /^(this product) has product samples enabled for all channels priced at ("[^"]+")$/
     * @Given /^the ("([^"]*)" product) has product samples enabled for all channels priced at ("[^"]+")$/
     */
    public function theProductHasSamplesEnabledForAllChannelsAtPrice(ProductInterface $product, int $price): void
    {
        $this->enableSamplesForAllChannelsWithPrice($product, $price);
    }

    private function enableSamplesForAllChannelsWithPrice(ProductInterface $product, int $price): void
    {
        $product->setSamplesActive(true);

        foreach ($product->getVariants() as $variant) {
            Assert::isInstanceOf($variant, ProductVariantInterface::class);

            if (null === $variant->getSample()) {
                /** @var ProductVariantInterface $sample */
                $sample = $this->productVariantFactory->createForProduct($product);
                $sample->setCode(sprintf('SAMPLE-%s', $variant->getCode() ?? ''));
                $sample->setSampleOf($variant);

                $variant->setSample($sample);
                $product->addVariant($sample);

                foreach ($product->getChannels() as $channel) {
                    $sample->addChannelPricing($this->createChannelPricingForChannel($price, $channel));
                }
            }
        }

        $this->objectManager->flush();
    }

    private function createChannelPricingForChannel(int $price, ChannelInterface $channel = null): ChannelPricingInterface
    {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        return $channelPricing;
    }
}
