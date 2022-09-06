<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\EventListener;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class SampleVariantGeneratorListener
{
    public function __construct(
        private FactoryInterface $channelPricingFactory,
        private ProductVariantFactoryInterface $productVariantFactory,
        private SampleVariantCodeGeneratorInterface $codeGenerator,
        private ProductVariantTranslationsSynchronizerInterface $translationsSynchronizer,
    ) {
    }

    public function ensureSampleVariantsExist(ResourceControllerEvent $event): void
    {
        /** @var ProductInterface $product */
        $product = $event->getSubject();

        Assert::isInstanceOf($product, ProductInterface::class);

        if (!$product->getSamplesActive()) {
            return;
        }

        foreach ($product->getVariants() as $variant) {
            Assert::isInstanceOf($variant, ProductVariantInterface::class);

            // Don't generate a sample if this variant is a sample of another variant
            if (null !== $variant->getSampleOf()) {
                continue;
            }

            // Don't generate a sample if this variant already has one
            if (null !== $variant->getSample()) {
                continue;
            }

            $this->generateSampleVariant($product, $variant);
        }
    }

    private function generateSampleVariant(ProductInterface $product, ProductVariantInterface $variant): void
    {
        /** @var ProductVariantInterface $sample */
        $sample = $this->productVariantFactory->createForProduct($product);
        $sample->setSampleOf($variant);
        $sample->setCode($this->codeGenerator->generate($sample));

        $variant->setSample($sample);

        $product->addVariant($sample);

        foreach ($product->getChannels() as $channel) {
            $sample->addChannelPricing($this->createChannelPricingForChannel(0, $channel));
        }

        $this->translationsSynchronizer->synchronize($sample);
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
