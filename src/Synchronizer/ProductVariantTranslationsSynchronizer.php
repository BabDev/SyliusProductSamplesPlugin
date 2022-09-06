<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Synchronizer;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantNameGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;

final class ProductVariantTranslationsSynchronizer implements ProductVariantTranslationsSynchronizerInterface
{
    public function __construct(
        private SampleVariantNameGeneratorInterface $nameGenerator,
    ) {
    }

    public function synchronize(ProductVariantInterface $sampleVariant): void
    {
        $actualVariant = $sampleVariant->getSampleOf();

        if (null === $actualVariant) {
            return;
        }

        // First, remove outdated translations from the sample
        /** @var ProductVariantTranslationInterface $translation */
        foreach ($sampleVariant->getTranslations() as $translation) {
            if (!$actualVariant->hasTranslation($translation)) {
                $sampleVariant->removeTranslation($translation);
            }
        }

        // Next, add missing translations to the sample
        /** @var ProductVariantTranslationInterface $translation */
        foreach ($actualVariant->getTranslations() as $translation) {
            if (!$sampleVariant->hasTranslation($translation)) {
                // Use the getter because the trait will create the appropriate model and set the relations
                $sampleVariant->getTranslation($translation->getLocale());
            }
        }

        // Finally, synchronize the names for the translations
        /** @var ProductVariantTranslationInterface $translation */
        foreach ($sampleVariant->getTranslations() as $translation) {
            $translation->setName($this->nameGenerator->generate($sampleVariant, $translation->getLocale()));
        }
    }
}
