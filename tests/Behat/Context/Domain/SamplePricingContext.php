<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Domain;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface as CoreProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Webmozart\Assert\Assert;

/**
 * The plugin takes no position on whether a catalog promotion ought to discount a sample — that is a
 * merchandising decision each store makes. What it can do is make the outcome knowable: these steps assert
 * what a promotion scoped at a product does to the samples underneath it, and that the plugin's own
 * storefront pricing reports the result rather than the pre-discount figure.
 */
final class SamplePricingContext implements Context
{
    public function __construct(
        private ProductVariantsPricesProviderInterface $productVariantsPricesProvider,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Then /^the sample of the ("[^"]+" variant) should be priced at ("(?:€|£|\$)[^"]+"), reduced from ("(?:€|£|\$)[^"]+")$/
     */
    public function theSampleOfTheVariantShouldBePricedAtReducedFrom(
        ProductVariantInterface $variant,
        int $price,
        int $originalPrice,
    ): void {
        $pricing = $this->getSampleOf($variant)->getChannelPricingForChannel($this->getChannel());

        Assert::notNull($pricing, 'Expected the sample to have channel pricing.');

        Assert::same(
            $pricing->getPrice(),
            $price,
            sprintf('Expected the sample to be priced at %d, got %s.', $price, var_export($pricing->getPrice(), true)),
        );

        Assert::same(
            $pricing->getOriginalPrice(),
            $originalPrice,
            sprintf('Expected the sample\'s original price to be %d, got %s.', $originalPrice, var_export($pricing->getOriginalPrice(), true)),
        );
    }

    /**
     * @Then /^the sample of the ("[^"]+" variant) should not be discounted$/
     */
    public function theSampleOfTheVariantShouldNotBeDiscounted(ProductVariantInterface $variant): void
    {
        $pricing = $this->getSampleOf($variant)->getChannelPricingForChannel($this->getChannel());

        Assert::notNull($pricing, 'Expected the sample to have channel pricing.');

        Assert::isEmpty(
            $pricing->getAppliedPromotions()->toArray(),
            'Expected no catalog promotion to have been applied to the sample.',
        );
    }

    /**
     * Reads the option map the storefront renders from, which is where the plugin appends its sample keys.
     *
     * @Then /^the storefront pricing for the ("[^"]+" variant) should offer its sample at ("(?:€|£|\$)[^"]+"), reduced from ("(?:€|£|\$)[^"]+")$/
     */
    public function theStorefrontPricingShouldOfferItsSampleAtReducedFrom(
        ProductVariantInterface $variant,
        int $price,
        int $originalPrice,
    ): void {
        $optionMap = $this->getOptionMapFor($variant);

        Assert::keyExists($optionMap, 'sample-price', 'Expected the storefront pricing to carry a sample price.');
        Assert::keyExists($optionMap, 'sample-original-price', 'Expected the storefront pricing to carry the sample\'s original price.');

        Assert::same($optionMap['sample-price'], $price, sprintf('Expected a sample price of %d.', $price));
        Assert::same($optionMap['sample-original-price'], $originalPrice, sprintf('Expected an original sample price of %d.', $originalPrice));
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getOptionMapFor(ProductVariantInterface $variant): array
    {
        $product = $variant->getProduct();

        Assert::isInstanceOf($product, ProductInterface::class);

        $prices = $this->productVariantsPricesProvider->provideVariantsPrices($product, $this->getChannel());

        /*
         * The provider matches its entries to the enabled variants by position, so the same ordering has to
         * be used to find the one under test.
         */
        $position = array_search($variant, array_values($product->getEnabledVariants()->toArray()), true);

        Assert::integer($position, sprintf('The "%s" variant is not among the product\'s enabled variants.', (string) $variant->getCode()));
        Assert::keyExists($prices, $position, 'The pricing provider returned no entry for the variant.');

        $optionMap = $prices[$position];

        Assert::isArray($optionMap);

        return $optionMap;
    }

    private function getChannel(): ChannelInterface
    {
        $channel = $this->sharedStorage->get('channel');

        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }

    private function getSampleOf(ProductVariantInterface $variant): CoreProductVariantInterface
    {
        $sample = $variant->getSample();

        Assert::isInstanceOf(
            $sample,
            CoreProductVariantInterface::class,
            sprintf('The "%s" variant has no sample, so there is nothing to assert about.', (string) $variant->getCode()),
        );

        return $sample;
    }
}
