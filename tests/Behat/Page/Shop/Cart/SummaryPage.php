<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Cart;

use Sylius\Behat\Page\Shop\Cart\SummaryPage as BaseSummaryPage;

class SummaryPage extends BaseSummaryPage implements SummaryPageInterface
{
    public function countItems(): int
    {
        return \count($this->getElement('cart_items')->findAll('css', '[data-test-cart-product-row]'));
    }
}
