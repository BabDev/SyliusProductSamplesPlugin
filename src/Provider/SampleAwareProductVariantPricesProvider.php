<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Provider;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface as CoreProductInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;

/**
 * Adds the sample pricing a product's storefront needs to the option maps built by Sylius.
 */
final class SampleAwareProductVariantPricesProvider implements ProductVariantsPricesProviderInterface
{
    public function __construct(
        private ProductVariantsPricesProviderInterface $decoratedProvider,
        private ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ) {
    }

    /**
     * @return array<array-key, mixed>
     */
    public function provideVariantsPrices(CoreProductInterface $product, ChannelInterface $channel): array
    {
        $variantsPrices = $this->decoratedProvider->provideVariantsPrices($product, $channel);

        if (!$product instanceof ProductInterface || !$product->getSamplesActive()) {
            return $variantsPrices;
        }

        $variants = array_values($product->getEnabledVariants()->toArray());

        /*
         * The decorated provider builds one entry per enabled variant, in iteration order, so the
         * entries line up with the collection by position. If that ever stops holding the sample
         * pricing is left out altogether, rather than being attached to the wrong variant.
         */
        if (\count($variants) !== \count($variantsPrices)) {
            return $variantsPrices;
        }

        foreach ($variants as $position => $variant) {
            if (!$variant instanceof ProductVariantInterface) {
                continue;
            }

            $sample = $variant->getSample();

            // A variant can be missing its sample when samples were activated after the variant was created
            if (!$sample instanceof ProductVariantInterface) {
                continue;
            }

            $optionMap = $variantsPrices[$position];

            if (!\is_array($optionMap)) {
                continue;
            }

            $samplePrice = $this->productVariantPricesCalculator->calculate($sample, ['channel' => $channel]);
            $sampleOriginalPrice = $this->productVariantPricesCalculator->calculateOriginal($sample, ['channel' => $channel]);

            $optionMap['sample-price'] = $samplePrice;

            // Mirrors how the decorated provider reports a discount on the variant itself
            if ($sampleOriginalPrice > $samplePrice) {
                $optionMap['sample-original-price'] = $sampleOriginalPrice;
            }

            $optionMap['free-sample'] = 0 === $samplePrice ? 'yes' : 'no';

            $variantsPrices[$position] = $optionMap;
        }

        return $variantsPrices;
    }
}
