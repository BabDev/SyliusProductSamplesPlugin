<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Ui\Admin;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\ProductVariant\CreatePageInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\ProductVariant\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingProductVariantsContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
    ) {
    }

    /**
     * @When /^I set its(?:| default) sample price to "(?:€|£|\$)([^"]+)" for ("([^"]+)" channel)$/
     * @When I do not set its sample price
     */
    public function iSetItsSamplePriceTo(?string $price = null, ?ChannelInterface $channel = null)
    {
        $this->createPage->specifySamplePrice($price ?? '', $channel ?? $this->sharedStorage->get('channel'));
    }

    /**
     * @Then /^the (variant with code "[^"]+") should have its sample priced at (?:€|£|\$)([^"]+) for (channel "([^"]+)")$/
     * @Then /^the (variant with code "[^"]+") should have its sample priced at "(?:€|£|\$)([^"]+)" for (channel "([^"]+)")$/
     */
    public function theVariantWithCodeShouldHaveItsSamplePricedAtForChannel(ProductVariantInterface $productVariant, string $price, ChannelInterface $channel)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);

        Assert::same($this->updatePage->getSamplePriceForChannel($channel), $price);
    }
}
