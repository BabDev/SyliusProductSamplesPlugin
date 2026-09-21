<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Webmozart\Assert\Assert;

/**
 * Covers the per-channel sample limit on the API's own add-to-cart path.
 */
final class CartContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @Then /^I should be told that I cannot request more than (\d+) sample per order$/
     * @Then /^I should be told that I cannot request more than (\d+) samples per order$/
     */
    public function iShouldBeToldThatICannotRequestMoreThanSamplesPerOrder(int $limit): void
    {
        $message = 1 === $limit
            ? 'Only 1 sample may be requested per order.'
            : sprintf('Only %d samples may be requested per order.', $limit);

        Assert::true(
            $this->responseChecker->hasViolationWithMessage($this->client->getLastResponse(), $message),
            sprintf(
                'Expected the response to carry the violation "%s", got %s.',
                $message,
                $this->describeViolations(),
            ),
        );
    }

    /**
     * Sylius words its own step "there should be N item in my cart", which reads wrong for any count above
     * one. This plural alias exists purely for legibility and cannot collide with it.
     *
     * @Then /^there should be (\d+) items in my (cart)$/
     */
    public function thereShouldBeItemsInMyCart(int $count, string $cartToken): void
    {
        $items = $this->responseChecker->getValue($this->client->show(Resources::ORDERS, $cartToken), 'items');

        Assert::isArray($items, 'Expected the cart to expose its items.');
        Assert::count($items, $count);
    }

    private function describeViolations(): string
    {
        $content = $this->responseChecker->getResponseContent($this->client->getLastResponse());
        $violations = $content['violations'] ?? [];

        if (!\is_array($violations) || [] === $violations) {
            return 'no violations at all';
        }

        $messages = [];

        foreach ($violations as $violation) {
            $message = \is_array($violation) ? ($violation['message'] ?? null) : null;

            $messages[] = \is_string($message) ? $message : '?';
        }

        return '[' . implode(' | ', $messages) . ']';
    }
}
