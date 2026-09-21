<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\EventListener;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantOptionValuesSynchronizerInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class SampleVariantGeneratorListener
{
    public function __construct(
        private readonly FactoryInterface $channelPricingFactory,
        private readonly ProductVariantFactoryInterface $productVariantFactory,
        private readonly SampleVariantCodeGeneratorInterface $codeGenerator,
        private readonly ProductVariantOptionValuesSynchronizerInterface $optionValuesSynchronizer,
        private readonly ProductVariantTranslationsSynchronizerInterface $translationsSynchronizer,
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
            if ($variant->getSampleOf() instanceof BaseProductVariantInterface) {
                continue;
            }

            // Don't generate a sample if this variant already has one
            if ($variant->getSample() instanceof BaseProductVariantInterface) {
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

        $this->optionValuesSynchronizer->synchronize($sample);
        $this->translationsSynchronizer->synchronize($sample);
    }

    private function createChannelPricingForChannel(int $price, ChannelInterface $channel): ChannelPricingInterface
    {
        $channelPricing = $this->channelPricingFactory->createNew();

        Assert::isInstanceOf($channelPricing, ChannelPricingInterface::class);

        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        return $channelPricing;
    }
}
