<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Domain;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductVariantInterface as CoreProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * The plugin defines no inventory integration for samples. What keeps them out of the admin inventory
 * screen today is incidental: the grid queries `createInventoryListQueryBuilder()`, which selects only
 * tracked variants, and nothing in the plugin ever marks a generated sample as tracked.
 *
 * These steps pin that incidental state so that defining a real integration later is a deliberate change
 * with a visible before and after, rather than a silent shift nobody notices.
 */
final class ManagingInventoryContext implements Context
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @Given /^the sample of the ("[^"]+" variant) is tracked by the inventory$/
     */
    public function theSampleOfTheVariantIsTrackedByTheInventory(ProductVariantInterface $variant): void
    {
        $this->getSampleOf($variant)->setTracked(true);

        $this->objectManager->flush();
    }

    /**
     * @Then /^the ("[^"]+" variant) should be tracked by the inventory$/
     */
    public function theVariantShouldBeTrackedByTheInventory(ProductVariantInterface $variant): void
    {
        Assert::true(
            $variant->isTracked(),
            sprintf('Expected the "%s" variant to be tracked.', (string) $variant->getCode()),
        );
    }

    /**
     * @Then /^the sample of the ("[^"]+" variant) should not be tracked by the inventory$/
     */
    public function theSampleOfTheVariantShouldNotBeTrackedByTheInventory(ProductVariantInterface $variant): void
    {
        $sample = $this->getSampleOf($variant);

        Assert::false(
            $sample->isTracked(),
            sprintf(
                'Expected the sample "%s" to be left untracked, because the plugin never marks one as tracked.',
                (string) $sample->getCode(),
            ),
        );
    }

    /**
     * @Then /^the inventory should not list the sample of the ("[^"]+" variant)$/
     */
    public function theInventoryShouldNotListTheSampleOfTheVariant(ProductVariantInterface $variant): void
    {
        $sampleCode = (string) $this->getSampleOf($variant)->getCode();

        Assert::false(
            \in_array($sampleCode, $this->inventoryCodes(), true),
            sprintf('Expected the sample "%s" to be absent from the inventory, got %s.', $sampleCode, $this->describeInventory()),
        );
    }

    /**
     * @Then /^the inventory should list the sample of the ("[^"]+" variant)$/
     */
    public function theInventoryShouldListTheSampleOfTheVariant(ProductVariantInterface $variant): void
    {
        $sampleCode = (string) $this->getSampleOf($variant)->getCode();

        Assert::inArray(
            $sampleCode,
            $this->inventoryCodes(),
            sprintf('Expected the sample "%s" in the inventory, got %s.', $sampleCode, $this->describeInventory()),
        );
    }

    /**
     * @return list<string|null>
     */
    private function inventoryCodes(): array
    {
        $codes = [];

        /** @var iterable<CoreProductVariantInterface> $variants */
        $variants = $this->productVariantRepository->createInventoryListQueryBuilder('en_US')->getQuery()->getResult();

        foreach ($variants as $variant) {
            $codes[] = $variant->getCode();
        }

        return $codes;
    }

    private function describeInventory(): string
    {
        $codes = array_map(static fn (?string $code): string => (string) $code, $this->inventoryCodes());

        return [] === $codes ? 'an empty inventory' : '[' . implode(', ', $codes) . ']';
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
}
