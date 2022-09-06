<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\EventListener;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class SampleVariantGeneratorListenerSpec extends ObjectBehavior
{
    private const DEFAULT_PRICE = 0;

    public function let(
        FactoryInterface $channelPricingFactory,
        ProductVariantFactoryInterface $productVariantFactory,
        SampleVariantCodeGeneratorInterface $codeGenerator,
        ProductVariantTranslationsSynchronizerInterface $translationsSynchronizer,
    ): void {
        $this->beConstructedWith($channelPricingFactory, $productVariantFactory, $codeGenerator, $translationsSynchronizer);
    }

    public function it_does_not_generate_sample_variants_if_product_samples_are_disabled(
        ResourceControllerEvent $event,
        ProductInterface $product,
    ): void {
        $event->getSubject()->willReturn($product);
        $product->getSamplesActive()->willReturn(false);

        $this->ensureSampleVariantsExist($event);
    }

    public function it_generates_sample_variants_if_product_samples_are_enabled_for_variants_without_samples(
        FactoryInterface $channelPricingFactory,
        ProductVariantFactoryInterface $productVariantFactory,
        SampleVariantCodeGeneratorInterface $codeGenerator,
        ProductVariantTranslationsSynchronizerInterface $translationsSynchronizer,
        ResourceControllerEvent $event,
        ProductInterface $product,
        ProductVariantInterface $variant1,
        ProductVariantInterface $variant2,
        ProductVariantInterface $sampleOfVariant2,
        ProductVariantInterface $variant3,
        ProductVariantInterface $newSampleOfVariant1,
        ProductVariantInterface $newSampleOfVariant3,
        ChannelInterface $channel,
        ChannelPricingInterface $newChannelPricingForSampleOfVariant1,
        ChannelPricingInterface $newChannelPricingForSampleOfVariant3,
    ): void {
        $newSampleOfVariant1Code = 'SAMPLE-variant-1';
        $newSampleOfVariant3Code = 'SAMPLE-variant-3';

        $productVariantFactory->createForProduct($product)->willReturn($newSampleOfVariant1, $newSampleOfVariant3);
        $codeGenerator->generate($newSampleOfVariant1)->willReturn($newSampleOfVariant1Code);
        $codeGenerator->generate($newSampleOfVariant3)->willReturn($newSampleOfVariant3Code);
        $channelPricingFactory->createNew()->willReturn($newChannelPricingForSampleOfVariant1, $newChannelPricingForSampleOfVariant3);

        $channel->getCode()->willReturn('web');

        $event->getSubject()->willReturn($product);

        $product->getSamplesActive()->willReturn(true);
        $product->getVariants()->willReturn(new ArrayCollection([$variant1->getWrappedObject(), $variant2->getWrappedObject(), $sampleOfVariant2->getWrappedObject(), $variant3->getWrappedObject()]));
        $product->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        /*
         * Variant 1
         */

        $variant1->getSampleOf()->willReturn(null);
        $variant1->getSample()->willReturn(null);

        $newSampleOfVariant1->setSampleOf($variant1)->shouldBeCalled();
        $newSampleOfVariant1->setCode($newSampleOfVariant1Code)->shouldBeCalled();

        $variant1->setSample($newSampleOfVariant1);

        $product->addVariant($newSampleOfVariant1);

        $newChannelPricingForSampleOfVariant1->setPrice(self::DEFAULT_PRICE)->shouldBeCalled();
        $newChannelPricingForSampleOfVariant1->setChannelCode('web')->shouldBeCalled();

        $newSampleOfVariant1->addChannelPricing($newChannelPricingForSampleOfVariant1)->shouldBeCalled();

        $translationsSynchronizer->synchronize($newSampleOfVariant1)->shouldBeCalled();

        /*
         * Variant 2
         */

        $variant2->getSampleOf()->willReturn(null);
        $variant2->getSample()->willReturn($sampleOfVariant2);

        /*
         * Sample of Variant 2
         */

        $sampleOfVariant2->getSampleOf()->willReturn($variant2);

        /*
         * Variant 3
         */

        $variant3->getSampleOf()->willReturn(null);
        $variant3->getSample()->willReturn(null);

        $newSampleOfVariant3->setCode($newSampleOfVariant3Code)->shouldBeCalled();
        $newSampleOfVariant3->setSampleOf($variant3)->shouldBeCalled();

        $variant3->setSample($newSampleOfVariant3);

        $product->addVariant($newSampleOfVariant3);

        $newChannelPricingForSampleOfVariant3->setPrice(self::DEFAULT_PRICE)->shouldBeCalled();
        $newChannelPricingForSampleOfVariant3->setChannelCode('web')->shouldBeCalled();

        $newSampleOfVariant3->addChannelPricing($newChannelPricingForSampleOfVariant3)->shouldBeCalled();

        $translationsSynchronizer->synchronize($newSampleOfVariant3)->shouldBeCalled();

        $this->ensureSampleVariantsExist($event);
    }
}
