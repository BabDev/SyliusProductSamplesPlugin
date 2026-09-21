<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Cart\SummaryPageInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Product\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    public function __construct(
        private readonly ShowPageInterface $showPage,
        private readonly SummaryPageInterface $cartSummaryPage,
    ) {
    }

    /**
     * @When I request a sample of this product
     * @When I request a sample
     */
    public function iRequestASample(): void
    {
        $this->showPage->requestASample();
    }

    /**
     * @When I try to request a sample of this product
     * @When I try to request a sample
     */
    public function iTryToRequestASample(): void
    {
        $this->showPage->tryToRequestASample();
    }

    /**
     * @Then I should be able to request a sample of this product
     */
    public function iShouldBeAbleToRequestASample(): void
    {
        Assert::true(
            $this->showPage->hasRequestASampleButton(),
            'Expected the product page to offer a sample, but the button is not there.',
        );
    }

    /**
     * @Then I should not be able to request a sample of this product
     */
    public function iShouldNotBeAbleToRequestASample(): void
    {
        Assert::false(
            $this->showPage->hasRequestASampleButton(),
            'Expected the product page not to offer a sample, but the button is there.',
        );
    }

    /**
     * @Then I should be offered a free sample
     */
    public function iShouldBeOfferedAFreeSample(): void
    {
        Assert::same($this->showPage->getRequestASampleButtonLabel(), 'Request a Free Sample');
    }

    /**
     * @Then I should be offered a sample priced at :price
     */
    public function iShouldBeOfferedASamplePricedAt(string $price): void
    {
        Assert::same($this->showPage->getRequestASampleButtonLabel(), sprintf('Request a Sample (%s)', $price));
    }

    /**
     * @Then I should be notified that I cannot request more than :limit sample per order
     * @Then I should be notified that I cannot request more than :limit samples per order
     */
    public function iShouldBeNotifiedOfTheSampleLimit(int $limit): void
    {
        $message = 1 === $limit
            ? 'Only 1 sample may be requested per order.'
            : sprintf('Only %d samples may be requested per order.', $limit);

        Assert::same($this->showPage->getCartValidationMessage(), $message);
    }

    /**
     * Sylius already covers the single-item case with "there should be one item in my cart"; a :count
     * alias for the singular would be ambiguous against it, so this step stays plural.
     *
     * @Then there should be :count items in my cart
     */
    public function thereShouldBeItemsInMyCart(int $count): void
    {
        Assert::same($this->cartSummaryPage->countItems(), $count);
    }
}
