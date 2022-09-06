<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Synchronizer;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;

interface ProductVariantOptionValuesSynchronizerInterface
{
    public function synchronize(ProductVariantInterface $sampleVariant): void;
}
