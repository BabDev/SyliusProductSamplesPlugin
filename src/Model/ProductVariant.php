<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

use Sylius\Component\Core\Model\ProductVariant as CoreProductVariant;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseProductVariantInterface;

class ProductVariant extends CoreProductVariant implements ProductVariantInterface
{
    protected ?BaseProductVariantInterface $sample = null;

    protected ?BaseProductVariantInterface $sampleOf = null;

    public function getSample(): ?BaseProductVariantInterface
    {
        return $this->sample;
    }

    public function setSample(?BaseProductVariantInterface $sample): void
    {
        $this->sample = $sample;
    }

    public function getSampleOf(): ?BaseProductVariantInterface
    {
        return $this->sampleOf;
    }

    public function setSampleOf(?BaseProductVariantInterface $sampleOf): void
    {
        $this->sampleOf = $sampleOf;
    }
}
