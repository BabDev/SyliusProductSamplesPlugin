<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\EventListener;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class SampleVariantCodeGeneratorListenerSpec extends ObjectBehavior
{
    public function it_generates_the_code_for_sample_variants_when_saving_products(
        ResourceControllerEvent $event,
        ProductInterface $product,
        ProductVariantInterface $variant1,
        ProductVariantInterface $variant2,
        ProductVariantInterface $sampleOfVariant2,
    ): void {
        $event->getSubject()->willReturn($product);

        $product->getVariants()->willReturn(new ArrayCollection([$variant1->getWrappedObject(), $variant2->getWrappedObject(), $sampleOfVariant2->getWrappedObject()]));

        /*
         * Variant 1
         */

        $variant1->getSample()->willReturn(null);

        /*
         * Variant 2
         */

        $variant2->getSample()->willReturn($sampleOfVariant2);
        $variant2->getCode()->willReturn('variant-2');
        $sampleOfVariant2->getCode()->willReturn(null);
        $sampleOfVariant2->setCode('SAMPLE-variant-2')->shouldBeCalled();

        /*
         * Sample of Variant 2
         */

        $sampleOfVariant2->getSample()->willReturn(null);

        $this->ensureSampleVariantsHaveCodes($event);
    }

    public function it_generates_the_code_for_sample_variants_when_saving_product_variants(
        ResourceControllerEvent $event,
        ProductVariantInterface $variant,
        ProductVariantInterface $sampleOfVariant,
    ): void {
        $event->getSubject()->willReturn($variant);

        $variant->getSample()->willReturn($sampleOfVariant);
        $variant->getCode()->willReturn('variant');
        $sampleOfVariant->getCode()->willReturn(null);
        $sampleOfVariant->setCode('SAMPLE-variant')->shouldBeCalled();

        $this->ensureSampleVariantsHaveCodes($event);
    }
}
