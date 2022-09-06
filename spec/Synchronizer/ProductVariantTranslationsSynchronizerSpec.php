<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Synchronizer;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;

final class ProductVariantTranslationsSynchronizerSpec extends ObjectBehavior
{
    public function let(SampleVariantNameGeneratorInterface $nameGenerator): void
    {
        $this->beConstructedWith($nameGenerator);
    }

    public function it_synchronizes_translations_from_the_variant_to_its_sample(
        SampleVariantNameGeneratorInterface $nameGenerator,
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $actualVariant,
        ProductVariantTranslationInterface $sampleVariantEnglishTranslation,
        ProductVariantTranslationInterface $sampleVariantGermanTranslation,
        ProductVariantTranslationInterface $sampleVariantFrenchTranslation,
        ProductVariantTranslationInterface $actualVariantEnglishTranslation,
        ProductVariantTranslationInterface $actualVariantFrenchTranslation,
    ): void {
        $sampleVariant->getSampleOf()->willReturn($actualVariant);
        $sampleVariant->getTranslations()->willReturn(
            new ArrayCollection([$sampleVariantEnglishTranslation->getWrappedObject(), $sampleVariantGermanTranslation->getWrappedObject()]), // First iteration to remove outdated translations
            new ArrayCollection([$sampleVariantEnglishTranslation->getWrappedObject(), $sampleVariantFrenchTranslation->getWrappedObject()]), // Second iteration to synchronize names
        );
        $actualVariant->hasTranslation($sampleVariantEnglishTranslation)->willReturn(true);
        $actualVariant->hasTranslation($sampleVariantGermanTranslation)->willReturn(false);
        $sampleVariant->removeTranslation($sampleVariantGermanTranslation)->shouldBeCalled();

        $actualVariant->getTranslations()->willReturn(new ArrayCollection([$actualVariantEnglishTranslation->getWrappedObject(), $actualVariantFrenchTranslation->getWrappedObject()]));
        $sampleVariant->hasTranslation($actualVariantEnglishTranslation)->willReturn(true);
        $sampleVariant->hasTranslation($actualVariantFrenchTranslation)->willReturn(false);
        $actualVariantFrenchTranslation->getLocale()->willReturn('fr_FR');
        $sampleVariant->getTranslation('fr_FR')->willReturn($sampleVariantFrenchTranslation);

        $englishVariantName = 'Sample - English';
        $frenchVariantName = 'Sample - French';

        $sampleVariantEnglishTranslation->getLocale()->willReturn('en_US');
        $nameGenerator->generate($sampleVariant, 'en_US')->willReturn($englishVariantName);
        $sampleVariantEnglishTranslation->setName($englishVariantName)->shouldBeCalled();

        $sampleVariantFrenchTranslation->getLocale()->willReturn('fr_FR');
        $nameGenerator->generate($sampleVariant, 'fr_FR')->willReturn($frenchVariantName);
        $sampleVariantFrenchTranslation->setName($frenchVariantName)->shouldBeCalled();

        $this->synchronize($sampleVariant);
    }
}
