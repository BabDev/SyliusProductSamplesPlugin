<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Product;

use Sylius\Behat\Page\Shop\Product\ShowPageInterface as BaseShowPageInterface;

interface ShowPageInterface extends BaseShowPageInterface
{
    public function hasRequestASampleButton(): bool;

    public function getRequestASampleButtonLabel(): string;

    /**
     * Requests a sample and waits for the cart summary the storefront script redirects to.
     */
    public function requestASample(): void;

    /**
     * Requests a sample and waits for the inline validation message instead of a redirect.
     */
    public function tryToRequestASample(): void;

    public function getCartValidationMessage(): string;
}
