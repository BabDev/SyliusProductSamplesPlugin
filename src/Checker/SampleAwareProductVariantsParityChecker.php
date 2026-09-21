<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Checker;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface as SampleAwareProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface as SampleAwareProductVariantInterface;
use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * Keeps a variant's own sample from being read as a duplicate of it.
 */
final class SampleAwareProductVariantsParityChecker implements ProductVariantsParityCheckerInterface
{
    public function __construct(private ProductVariantsParityCheckerInterface $decorated)
    {
    }

    public function checkParity(ProductVariantInterface $variant, ProductInterface $product): bool
    {
        if (!$product instanceof SampleAwareProductInterface) {
            return $this->decorated->checkParity($variant, $product);
        }

        if ($variant instanceof SampleAwareProductVariantInterface && null !== $variant->getSampleOf()) {
            return false;
        }

        /*
         * The comparison below mirrors the upstream checker, narrowed to the product's non-sample variants.
         * The upstream class exposes no seam for the candidate set — it reads $product->getVariants() directly —
         * so the loop has to be carried here. Diff it against the upstream file when upgrading Sylius.
         */
        foreach ($product->getNonSampleVariants() as $existingVariant) {
            if ($variant === $existingVariant || \count($variant->getOptionValues()) !== \count($product->getOptions())) {
                continue;
            }

            if ($this->matchOptions($variant, $existingVariant)) {
                return true;
            }
        }

        return false;
    }

    private function matchOptions(ProductVariantInterface $variant, ProductVariantInterface $existingVariant): bool
    {
        foreach ($variant->getOptionValues() as $option) {
            if (!$existingVariant->hasOptionValue($option)) {
                return false;
            }
        }

        return true;
    }
}
