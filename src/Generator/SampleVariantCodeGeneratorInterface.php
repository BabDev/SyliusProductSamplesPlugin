<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;

interface SampleVariantCodeGeneratorInterface
{
    /**
     * Builds the code for a sample variant, which must be unique across all product variants.
     */
    public function generate(ProductVariantInterface $sampleVariant): string;
}
