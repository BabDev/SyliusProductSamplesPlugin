<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Ui\Admin;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product\CreateSimpleProductPageInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
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
     * @When /^I set its(?:| default) sample price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iSetItsSamplePriceTo(string $price, ChannelInterface $channel): void
    {
        $this->createSimpleProductPage->specifySamplePrice($channel, $price);
    }

    /**
     * @When /^I set its original sample price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iSetItsOriginalSamplePriceTo(int $originalPrice, ChannelInterface $channel): void
    {
        $this->createSimpleProductPage->specifyOriginalSamplePrice($channel, $originalPrice);
    }

    /**
     * @When I set its sample shipping category as :shippingCategoryName
     */
    public function iSetItsSampleShippingCategoryAs(string $shippingCategoryName): void
    {
        $this->createSimpleProductPage->selectSampleShippingCategory($shippingCategoryName);
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
     * @When /^I change its sample price to (?:€|£|\$)([^"]+) for ("([^"]+)" channel)$/
     */
    public function iChangeItsSamplePriceTo(string $price, ChannelInterface $channel): void
    {
        $this->updateSimpleProductPage->specifySamplePrice($channel, $price);
    }

    /**
     * @When /^I change its original sample price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     */
    public function iChangeItsOriginalSamplePriceTo(string $price, ChannelInterface $channel)
    {
        $this->updateSimpleProductPage->specifyOriginalSamplePrice($channel, $price);
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

    /**
     * @Then /^(it|this product) should have its sample priced at (?:€|£|\$)([^"]+) for (channel "([^"]+)")$/
     * @Then /^(product "[^"]+") should have its sample priced at (?:€|£|\$)([^"]+) for (channel "([^"]+)")$/
     */
    public function itShouldBePricedAtForChannel(ProductInterface $product, string $price, ChannelInterface $channel): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same($this->updateSimpleProductPage->getSamplePriceForChannel($channel), $price);
    }

    /**
     * @Then /^(its|this products) original sample price should be "(?:€|£|\$)([^"]+)" for (channel "([^"]+)")$/
     */
    public function itsOriginalSamplePriceForChannel(ProductInterface $product, string $originalPrice, ChannelInterface $channel)
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::same(
            $this->updateSimpleProductPage->getOriginalSamplePriceForChannel($channel),
            $originalPrice
        );
    }
}
