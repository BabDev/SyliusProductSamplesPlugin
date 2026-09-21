<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\Product as CoreProduct;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseProductVariantInterface;

class Product extends CoreProduct implements ProductInterface
{
    protected bool $samplesActive = false;

    public function getSamplesActive(): bool
    {
        return $this->samplesActive;
    }

    public function setSamplesActive(bool $samplesActive): void
    {
        $this->samplesActive = $samplesActive;
    }

    public function getNonSampleVariants(): Collection
    {
        $variants = $this->variants->filter(static function (BaseProductVariantInterface $productVariant) {
            if (!$productVariant instanceof ProductVariantInterface) {
                return true;
            }

            return null === $productVariant->getSampleOf();
        });

        /*
         * Collection::filter() preserves the keys of the collection it filters, which leaves gaps wherever a
         * sample was removed. The admin variant generation form maps onto this collection and names its rows
         * by key, so the gaps would surface as missing rows; reindex to keep it a list.
         */
        /** @var array<array-key, ProductVariantInterface> $nonSampleVariants */
        $nonSampleVariants = array_values($variants->toArray());

        return new ArrayCollection($nonSampleVariants);
    }

    public function addNonSampleVariant(BaseProductVariantInterface $variant): void
    {
        $this->addVariant($variant);
    }

    public function removeNonSampleVariant(BaseProductVariantInterface $variant): void
    {
        $this->removeVariant($variant);
    }

    public function getEnabledVariants(): Collection
    {
        return $this->variants->filter(static function (BaseProductVariantInterface $productVariant) {
            if (!$productVariant->isEnabled()) {
                return false;
            }

            if (!$productVariant instanceof ProductVariantInterface) {
                return true;
            }

            return null === $productVariant->getSampleOf();
        });
    }

    public function isSimple(): bool
    {
        // Filter out variants that are samples
        return 1 === $this->variants->filter(static function (BaseProductVariantInterface $productVariant): bool {
            if (!$productVariant instanceof ProductVariantInterface) {
                return true;
            }

            return null === $productVariant->getSampleOf();
        })->count() && !$this->hasOptions();
    }
}
