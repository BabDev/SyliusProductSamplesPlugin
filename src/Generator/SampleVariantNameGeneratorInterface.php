<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;

interface SampleVariantNameGeneratorInterface
{
    public function generate(ProductVariantInterface $sampleVariant, ?string $locale = null): string;
}
