<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseProductVariantInterface;

interface ProductInterface extends BaseProductInterface
{
    public function getSamplesActive(): bool;

    public function setSamplesActive(bool $samplesActive): void;

    /**
     * @return Collection<array-key, ProductVariantInterface>
     */
    public function getNonSampleVariants(): Collection;

    /**
     * Adds a variant through the non-sample view of the variant collection.
     *
     * Exists so the admin variant generation form, which is mapped to `nonSampleVariants`, can write
     * its collection back without the property accessor treating every sample as a removal.
     */
    public function addNonSampleVariant(BaseProductVariantInterface $variant): void;

    public function removeNonSampleVariant(BaseProductVariantInterface $variant): void;
}
