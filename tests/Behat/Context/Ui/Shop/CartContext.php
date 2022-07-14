<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Ui\Shop;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use Behat\Behat\Context\Context;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Shop\Product\ShowPageInterface;

final class CartContext implements Context
{
    public function __construct(
        private ShowPageInterface $productShowPage,
    ) {
    }

    /**
     * @Given I have a sample of :product with :productOption :productOptionValue in the cart
     * @Given I have a sample of product :product with product option :productOption :productOptionValue in the cart
     * @When I request a sample of :product with :productOption :productOptionValue
     */
    public function iRequestASampleOfThisProduct(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        string $productOptionValue
    ): void {
        $this->productShowPage->open(['slug' => $product->getSlug()]);

        $this->productShowPage->requestASampleWithOption($productOption, $productOptionValue);
    }
}
