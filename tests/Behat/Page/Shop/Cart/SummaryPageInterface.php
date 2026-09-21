<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Cart;

use Sylius\Behat\Page\Shop\Cart\SummaryPageInterface as BaseSummaryPageInterface;

interface SummaryPageInterface extends BaseSummaryPageInterface
{
    /**
     * Sylius only exposes whether the cart holds exactly one item, which is not enough to assert a
     * per-order sample limit that allows more than one.
     */
    public function countItems(): int;
}
