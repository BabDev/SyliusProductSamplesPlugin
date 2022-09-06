<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\EventListener;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class SampleVariantGeneratorListener
{
    public function __construct(
        private FactoryInterface $channelPricingFactory,
        private ProductVariantFactoryInterface $productVariantFactory,
        private SampleVariantCodeGeneratorInterface $codeGenerator,
        private SampleVariantNameGeneratorInterface $nameGenerator,
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

        // Ensure translations exist for the sample
        /** @var ProductVariantTranslationInterface $translation */
        foreach ($variant->getTranslations() as $translation) {
            if (!$sample->hasTranslation($translation)) {
                // Use the getter because the trait will create the appropriate model and set the relations
                $sample->getTranslation($translation->getLocale());
            }
        }

        // Then synchronize the names for the translations
        /** @var ProductVariantTranslationInterface $translation */
        foreach ($sample->getTranslations() as $translation) {
            $translation->setName($this->nameGenerator->generate($sample, $translation->getLocale()));
        }
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
