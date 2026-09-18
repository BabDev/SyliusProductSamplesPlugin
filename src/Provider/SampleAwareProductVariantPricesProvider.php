<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Provider;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface as CoreProductInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Webmozart\Assert\Assert;

final class SampleAwareProductVariantPricesProvider implements ProductVariantsPricesProviderInterface
{
    public function __construct(private ProductVariantPriceCalculatorInterface $productVariantPriceCalculator)
    {
    }

    public function provideVariantsPrices(CoreProductInterface $product, ChannelInterface $channel): array
    {
        Assert::isInstanceOf($product, ProductInterface::class);

        $variantsPrices = [];

        /** @var ProductVariantInterface $variant */
        foreach ($product->getEnabledVariants() as $variant) {
            Assert::isInstanceOf($variant, ProductVariantInterface::class);

            $variantsPrices[] = $this->constructOptionsMap($product, $variant, $channel);
        }

        return $variantsPrices;
    }

    private function constructOptionsMap(ProductInterface $product, ProductVariantInterface $variant, ChannelInterface $channel): array
    {
        $optionMap = [];

        /** @var ProductOptionValueInterface $option */
        foreach ($variant->getOptionValues() as $option) {
            $optionMap[$option->getOptionCode()] = $option->getCode();
        }

        $price = $this->productVariantPriceCalculator->calculate($variant, ['channel' => $channel]);
        $optionMap['value'] = $price;

        if ($this->productVariantPriceCalculator instanceof ProductVariantPricesCalculatorInterface) {
            $originalPrice = $this->productVariantPriceCalculator->calculateOriginal($variant, ['channel' => $channel]);

            if ($originalPrice > $price) {
                $optionMap['original-price'] = $originalPrice;
            }
        }

        /** @var ArrayCollection $appliedPromotions */
        $appliedPromotions = $variant->getAppliedPromotionsForChannel($channel);
        if (!$appliedPromotions->isEmpty()) {
            $optionMap['applied_promotions'] = $appliedPromotions->toArray();
        }

        if ($product->getSamplesActive()) {
            $sample = $variant->getSample();

            /*
             * A variant can be missing its sample when samples were activated after the variant was
             * created; leaving the keys out lets the storefront fall back to its generic sample label
             * instead of failing to render the product.
             */
            if ($sample instanceof ProductVariantInterface) {
                $samplePrice = $this->productVariantPriceCalculator->calculate($sample, ['channel' => $channel]);

                $optionMap['sample-price'] = $samplePrice;
                $optionMap['free-sample'] = 0 === $samplePrice ? 'yes' : 'no';
            }
        }

        return $optionMap;
    }
}
