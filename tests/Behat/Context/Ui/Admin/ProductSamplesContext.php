<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Ui\Admin;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product\CreateSimpleProductPageInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Webmozart\Assert\Assert;

final class ProductSamplesContext implements Context
{
    public function __construct(
        private ObjectManager $objectManager,
        private CreateSimpleProductPageInterface $createSimpleProductPage,
        private UpdateSimpleProductPageInterface $updateSimpleProductPage,
    ) {
    }

    /**
     * @Given /^the ("([^"]*)" product) has product samples enabled$/
     */
    public function theProductHasSamplesEnabled(ProductInterface $product): void
    {
        $product->setSamplesActive(true);

        $this->objectManager->flush();
    }

    /**
     * @When /^I enable product samples$/
     */
    public function iEnableProductSamples(): void
    {
        $this->createSimpleProductPage->enableProductSamples();
    }

    /**
     * @When /^I disable product samples for it$/
     */
    public function iDisableProductSamplesForIt(): void
    {
        $this->updateSimpleProductPage->disableProductSamples();
    }

    /**
     * @When /^I enable product samples for it$/
     */
    public function iEnableProductSamplesForIt(): void
    {
        $this->updateSimpleProductPage->enableProductSamples();
    }

    /**
     * @Then /^(it|this product) should have product samples disabled$/
     * @Then /^(product "[^"]+") should have product samples disabled$/
     */
    public function itShouldHaveProductSamplesDisabled(ProductInterface $product): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::false($this->updateSimpleProductPage->hasProductSamplesEnabled());
    }

    /**
     * @Then /^(it|this product) should have product samples enabled$/
     * @Then /^(product "[^"]+") should have product samples enabled$/
     */
    public function itShouldHaveProductSamplesEnabled(ProductInterface $product): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::true($this->updateSimpleProductPage->hasProductSamplesEnabled());
    }
}
