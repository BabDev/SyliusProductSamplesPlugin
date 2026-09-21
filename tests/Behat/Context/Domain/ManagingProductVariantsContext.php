<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Domain;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface as BaseProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * Exercises the repository directly rather than through a page, because the searches under test back an
 * autocomplete endpoint rather than a rendered list: the admin never sees this data as markup.
 */
final class ManagingProductVariantsContext implements Context
{
    /** @var list<string|null> */
    private array $foundCodes = [];

    public function __construct(private readonly ProductVariantRepositoryInterface $productVariantRepository)
    {
    }

    /**
     * @When /^I search all product variants for "([^"]+)"$/
     */
    public function iSearchAllProductVariantsFor(string $phrase): void
    {
        $this->rememberResults($this->productVariantRepository->findByPhrase($phrase, 'en_US'));
    }

    /**
     * @When /^I search the variants of (this product) for "([^"]+)"$/
     * @When /^I search the variants of the ("[^"]+" product) for "([^"]+)"$/
     */
    public function iSearchTheVariantsOfTheProductFor(ProductInterface $product, string $phrase): void
    {
        $this->rememberResults(
            $this->productVariantRepository->findByPhraseAndProductCode($phrase, 'en_US', (string) $product->getCode()),
        );
    }

    /**
     * @Then /^the ("[^"]+" variant) should be among the results$/
     */
    public function theVariantShouldBeAmongTheResults(ProductVariantInterface $variant): void
    {
        Assert::inArray(
            $variant->getCode(),
            $this->foundCodes,
            sprintf(
                'Expected the "%s" variant to be among the search results, got %s.',
                (string) $variant->getCode(),
                $this->describeResults(),
            ),
        );
    }

    /**
     * @Then /^the sample of the ("[^"]+" variant) should not be among the results$/
     */
    public function theSampleOfTheVariantShouldNotBeAmongTheResults(ProductVariantInterface $variant): void
    {
        $sample = $this->getSampleOf($variant);

        Assert::false(
            \in_array($sample->getCode(), $this->foundCodes, true),
            sprintf(
                'Expected the sample "%s" to be left out of the search results, got %s.',
                (string) $sample->getCode(),
                $this->describeResults(),
            ),
        );
    }

    /**
     * @Then /^the sample of the ("[^"]+" variant) should still be found by its code$/
     */
    public function theSampleOfTheVariantShouldStillBeFoundByItsCode(ProductVariantInterface $variant): void
    {
        $sample = $this->getSampleOf($variant);
        $sampleCode = (string) $sample->getCode();
        $productCode = (string) $variant->getProduct()?->getCode();

        Assert::count(
            $this->productVariantRepository->findByCodes([$sampleCode]),
            1,
            sprintf('Expected findByCodes() to still resolve the sample "%s".', $sampleCode),
        );

        Assert::notNull(
            $this->productVariantRepository->findOneByCodeAndProductCode($sampleCode, $productCode),
            sprintf('Expected findOneByCodeAndProductCode() to still resolve the sample "%s".', $sampleCode),
        );

        Assert::count(
            $this->productVariantRepository->findByCodesAndProductCode([$sampleCode], $productCode),
            1,
            sprintf('Expected findByCodesAndProductCode() to still resolve the sample "%s".', $sampleCode),
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

    /**
     * @param array<array-key, mixed> $results
     */
    private function rememberResults(array $results): void
    {
        $this->foundCodes = [];

        foreach ($results as $result) {
            Assert::isInstanceOf($result, BaseProductVariantInterface::class);

            $this->foundCodes[] = $result->getCode();
        }
    }

    private function describeResults(): string
    {
        if ([] === $this->foundCodes) {
            return 'no results';
        }

        return '[' . implode(', ', array_map(static fn (?string $code): string => (string) $code, $this->foundCodes)) . ']';
    }
}
