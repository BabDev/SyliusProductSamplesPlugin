<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantTranslationsSynchronizerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Symfony\Component\Form\FormEvent;

final class SynchronizeSampleProductVariantTranslationsFormSubscriberSpec extends ObjectBehavior
{
    public function let(ProductVariantTranslationsSynchronizerInterface $translationsSynchronizer): void
    {
        $this->beConstructedWith($translationsSynchronizer);
    }

    public function it_does_nothing_when_there_is_no_data(FormEvent $event): void
    {
        $event->getData()->willReturn(null);

        $this->onSubmit($event);
    }

    public function it_synchronizes_translations_from_the_variant_to_its_sample(
        ProductVariantTranslationsSynchronizerInterface $translationsSynchronizer,
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

        $translationsSynchronizer->synchronize($sampleVariant)->shouldBeCalled();

        $this->onSubmit($event);
    }
}
