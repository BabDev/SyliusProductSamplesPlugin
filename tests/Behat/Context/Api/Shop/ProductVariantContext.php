<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Api\Shop;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductVariantContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When /^I browse the available product variants$/
     */
    public function iBrowseTheAvailableProductVariants(): void
    {
        $this->client->index(Resources::PRODUCT_VARIANTS);
    }

    /**
     * @When /^I ask for the sample of the ("[^"]+" variant) directly$/
     */
    public function iAskForTheSampleOfTheVariantDirectly(ProductVariantInterface $variant): void
    {
        $this->client->show(Resources::PRODUCT_VARIANTS, (string) $this->getSampleOf($variant)->getCode());
    }

    /**
     * @When /^I ask for the ("[^"]+" product)$/
     */
    public function iAskForTheProduct(ProductInterface $product): void
    {
        $this->client->show(Resources::PRODUCTS, (string) $product->getCode());
    }

    /**
     * @Then /^the ("[^"]+" variant) should be among them$/
     */
    public function theVariantShouldBeAmongThem(ProductVariantInterface $variant): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'code', (string) $variant->getCode()),
            sprintf('Expected the "%s" variant in the collection, got %s.', (string) $variant->getCode(), $this->describeCollection()),
        );
    }

    /**
     * @Then /^the sample of the ("[^"]+" variant) should not be among them$/
     */
    public function theSampleOfTheVariantShouldNotBeAmongThem(ProductVariantInterface $variant): void
    {
        $sampleCode = (string) $this->getSampleOf($variant)->getCode();

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'code', $sampleCode),
            sprintf('Expected the sample "%s" to be left out of the collection, got %s.', $sampleCode, $this->describeCollection()),
        );
    }

    /**
     * @Then /^I should be given its details$/
     */
    public function iShouldBeGivenItsDetails(): void
    {
        Assert::true(
            $this->responseChecker->isShowSuccessful($this->client->getLastResponse()),
            'Expected the sample to be readable by its own IRI, because a cart item can point at one.',
        );
    }

    /**
     * @Then /^its variants should not include the sample of the ("[^"]+" variant)$/
     */
    public function itsVariantsShouldNotIncludeTheSampleOfTheVariant(ProductVariantInterface $variant): void
    {
        $sampleCode = (string) $this->getSampleOf($variant)->getCode();
        $variantIris = $this->responseChecker->getValue($this->client->getLastResponse(), 'variants');

        Assert::isArray($variantIris, 'Expected the product to expose a list of variants.');
        Assert::notEmpty($variantIris, 'Expected the product to expose at least its non-sample variant.');

        /** @var list<string> $variantIris */
        $variantIris = array_map(static fn (mixed $iri): string => \is_string($iri) ? $iri : '', $variantIris);

        foreach ($variantIris as $iri) {
            Assert::notSame(
                basename($iri),
                $sampleCode,
                sprintf('Expected the sample "%s" to be left out of the product\'s variants, got %s.', $sampleCode, implode(', ', $variantIris)),
            );
        }
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
