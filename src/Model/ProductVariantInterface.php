<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

use Sylius\Component\Core\Model\ProductVariantInterface as CoreProductVariantInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseProductVariantInterface;

interface ProductVariantInterface extends CoreProductVariantInterface
{
    public function getSample(): ?BaseProductVariantInterface;

    public function setSample(?BaseProductVariantInterface $sample): void;

    public function getSampleOf(): ?BaseProductVariantInterface;

    public function setSampleOf(?BaseProductVariantInterface $sampleOf): void;
}
