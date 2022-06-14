<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;

interface SampleVariantCodeGeneratorInterface
{
    public function generate(ProductVariantInterface $sampleVariant): string;
}
