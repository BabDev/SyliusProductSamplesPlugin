<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Synchronizer;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductVariantOptionValuesSynchronizerSpec extends ObjectBehavior
{
    public function it_synchronizes_option_values_from_the_variant_to_its_sample(
        SampleVariantNameGeneratorInterface $nameGenerator,
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $actualVariant,
        ProductOptionValueInterface $smallOptionValue,
        ProductOptionValueInterface $mediumOptionValue,
        ProductOptionValueInterface $largeOptionValue,
    ): void {
        $sampleVariant->getSampleOf()->willReturn($actualVariant);
        $sampleVariant->getOptionValues()->willReturn(new ArrayCollection([$smallOptionValue->getWrappedObject(), $mediumOptionValue->getWrappedObject()]));

        $actualVariant->hasOptionValue($smallOptionValue)->willReturn(true);
        $actualVariant->hasOptionValue($mediumOptionValue)->willReturn(false);
        $sampleVariant->removeOptionValue($mediumOptionValue)->shouldBeCalled();

        $actualVariant->getOptionValues()->willReturn(new ArrayCollection([$smallOptionValue->getWrappedObject(), $largeOptionValue->getWrappedObject()]));
        $sampleVariant->hasOptionValue($smallOptionValue)->willReturn(true);
        $sampleVariant->hasOptionValue($largeOptionValue)->willReturn(false);
        $sampleVariant->addOptionValue($largeOptionValue)->shouldBeCalled();

        $this->synchronize($sampleVariant);
    }
}
