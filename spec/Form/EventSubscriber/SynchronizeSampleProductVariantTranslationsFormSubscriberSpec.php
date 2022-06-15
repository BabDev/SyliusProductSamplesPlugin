<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Symfony\Component\Form\FormEvent;

final class SynchronizeSampleProductVariantTranslationsFormSubscriberSpec extends ObjectBehavior
{
    public function it_does_nothing_when_there_is_no_data(FormEvent $event): void
    {
        $event->getData()->willReturn(null);

        $this->onSubmit($event);
    }

    public function it_synchronizes_translations_from_the_variant_to_its_sample(
        FormEvent $event,
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $actualVariant,
        ProductVariantTranslationInterface $sampleVariantEnglishTranslation,
        ProductVariantTranslationInterface $sampleVariantGermanTranslation,
        ProductVariantTranslationInterface $sampleVariantFrenchTranslation,
        ProductVariantTranslationInterface $actualVariantEnglishTranslation,
        ProductVariantTranslationInterface $actualVariantFrenchTranslation,
    ): void {
        $event->getData()->willReturn($sampleVariant);

        $sampleVariant->getSampleOf()->willReturn($actualVariant);
        $sampleVariant->getTranslations()->willReturn(new ArrayCollection([$sampleVariantEnglishTranslation->getWrappedObject(), $sampleVariantGermanTranslation->getWrappedObject()]));
        $actualVariant->hasTranslation($sampleVariantEnglishTranslation)->willReturn(true);
        $actualVariant->hasTranslation($sampleVariantGermanTranslation)->willReturn(false);
        $sampleVariant->removeTranslation($sampleVariantGermanTranslation)->shouldBeCalled();

        $actualVariant->getTranslations()->willReturn(new ArrayCollection([$actualVariantEnglishTranslation->getWrappedObject(), $actualVariantFrenchTranslation->getWrappedObject()]));
        $sampleVariant->hasTranslation($actualVariantEnglishTranslation)->willReturn(true);
        $sampleVariant->hasTranslation($actualVariantFrenchTranslation)->willReturn(false);
        $actualVariantFrenchTranslation->getLocale()->willReturn('fr_FR');
        $sampleVariant->getTranslation('fr_FR')->willReturn($sampleVariantFrenchTranslation);

        $this->onSubmit($event);
    }
}
