<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Product;

use Behat\Mink\Session;
use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface;
use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;
use Symfony\Component\Routing\RouterInterface;

class ShowPage extends BaseShowPage implements ShowPageInterface
{
    /**
     * The parent holds its own reference to the cart summary page privately, so this class keeps one too
     * rather than reaching into it.
     */
    private SummaryPageInterface $cartSummaryPage;

    /**
     * @param array<string, mixed>|\ArrayAccess<string, mixed> $minkParameters
     */
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        SummaryPageInterface $summaryPage,
    ) {
        parent::__construct($session, $minkParameters, $router, $summaryPage);

        $this->cartSummaryPage = $summaryPage;
    }

    public function hasRequestASampleButton(): bool
    {
        return $this->hasElement('request_a_sample_button');
    }

    public function getRequestASampleButtonLabel(): string
    {
        return trim($this->getElement('request_a_sample_button')->getText());
    }

    public function requestASample(): void
    {
        $this->getElement('request_a_sample_button')->click();

        /*
         * The plugin's storefront script submits with fetch() and then redirects, so there is no jQuery
         * activity to wait on — the cart summary being open is the signal that the request succeeded.
         */
        $this->getDocument()->waitFor(5, fn (): bool => $this->cartSummaryPage->isOpen());
    }

    public function tryToRequestASample(): void
    {
        $this->getElement('request_a_sample_button')->click();

        $this->getDocument()->waitFor(5, fn (): bool => '' !== $this->getCartValidationMessage());
    }

    public function getCartValidationMessage(): string
    {
        if (!$this->hasElement('validation_errors')) {
            return '';
        }

        return trim($this->getElement('validation_errors')->getText());
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'request_a_sample_button' => '[data-test-request-a-sample-button]',
        ]);
    }
}
