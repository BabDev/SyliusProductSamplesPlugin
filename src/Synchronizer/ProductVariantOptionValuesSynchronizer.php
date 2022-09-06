<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Synchronizer;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;

final class ProductVariantOptionValuesSynchronizer implements ProductVariantOptionValuesSynchronizerInterface
{
    public function synchronize(ProductVariantInterface $sampleVariant): void
    {
        $actualVariant = $sampleVariant->getSampleOf();

        if (null === $actualVariant) {
            return;
        }

        // First, remove outdated option values from the sample
        foreach ($sampleVariant->getOptionValues() as $optionValue) {
            if (!$actualVariant->hasOptionValue($optionValue)) {
                $sampleVariant->removeOptionValue($optionValue);
            }
        }

        // Next, add missing option values to the sample
        foreach ($actualVariant->getOptionValues() as $optionValue) {
            if (!$sampleVariant->hasOptionValue($optionValue)) {
                $sampleVariant->addOptionValue($optionValue);
            }
        }
    }
}
