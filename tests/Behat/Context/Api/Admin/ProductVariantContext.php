<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Api\Admin;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * The counterpart to the shop API context: everything hidden from a shopper has to stay visible to an
 * administrator, who manages samples through the very screens these endpoints back.
 */
final class ProductVariantContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When /^I browse the product variants$/
     */
    public function iBrowseTheProductVariants(): void
    {
        $this->client->index(Resources::PRODUCT_VARIANTS);
    }

    /**
     * @When /^I request the ("[^"]+" product)$/
     */
    public function iRequestTheProduct(ProductInterface $product): void
    {
        $this->client->show(Resources::PRODUCTS, (string) $product->getCode());
    }

    /**
     * @Then /^the sample of the ("[^"]+" variant) should be listed$/
     */
    public function theSampleOfTheVariantShouldBeListed(ProductVariantInterface $variant): void
    {
        $sampleCode = (string) $this->getSampleOf($variant)->getCode();

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'code', $sampleCode),
            sprintf('Expected the sample "%s" to stay visible to an administrator, got %s.', $sampleCode, $this->describeCollection()),
        );
    }

    /**
     * @Then /^its variants should include the sample of the ("[^"]+" variant)$/
     */
    public function itsVariantsShouldIncludeTheSampleOfTheVariant(ProductVariantInterface $variant): void
    {
        $sampleCode = (string) $this->getSampleOf($variant)->getCode();
        $variantIris = $this->responseChecker->getValue($this->client->getLastResponse(), 'variants');

        Assert::isArray($variantIris, 'Expected the product to expose a list of variants.');

        /** @var list<string> $variantIris */
        $variantIris = array_map(static fn (mixed $iri): string => \is_string($iri) ? $iri : '', $variantIris);

        $codes = array_map(static fn (string $iri): string => basename($iri), $variantIris);

        Assert::inArray(
            $sampleCode,
            $codes,
            sprintf('Expected the sample "%s" among the product\'s variants, got %s.', $sampleCode, implode(', ', $codes)),
        );
    }

    private function getSampleOf(ProductVariantInterface $variant): ProductVariantInterface
    {
        $sample = $variant->getSample();

        Assert::isInstanceOf(
            $sample,
            ProductVariantInterface::class,
            sprintf('The "%s" variant has no sample, so there is nothing to assert about.', (string) $variant->getCode()),
        );

        return $sample;
    }

    private function describeCollection(): string
    {
        $codes = [];

        foreach ($this->responseChecker->getCollection($this->client->getLastResponse()) as $item) {
            $code = \is_array($item) ? ($item['code'] ?? null) : null;

            $codes[] = \is_string($code) ? $code : '?';
        }

        return [] === $codes ? 'an empty collection' : '[' . implode(', ', $codes) . ']';
    }
}
