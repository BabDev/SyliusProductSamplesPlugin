<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Ui\Admin;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use Behat\Behat\Context\Context;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Channel\CreatePageInterface;
use Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Channel\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingChannelsContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
    ) {
    }

    /**
     * @When I set the max number of samples per order to :maxSamplesPerOrder
     */
    public function iSetTheMaxNumberOfSamplesPerOrderTo(string $maxSamplesPerOrder): void
    {
        $this->createPage->setMaxSamplesPerOrder($maxSamplesPerOrder);
    }

    /**
     * @When I set the sample product code prefix to :sampleProductCodePrefix
     */
    public function iSetTheSampleProductCodePrefixTo(string $sampleProductCodePrefix): void
    {
        $this->createPage->setSampleProductCodePrefix($sampleProductCodePrefix);
    }

    /**
     * @When I set its max number of samples per order to :maxSamplesPerOrder
     */
    public function iSetItsMaxNumberOfSamplesPerOrderTo(string $maxSamplesPerOrder): void
    {
        $this->updatePage->setMaxSamplesPerOrder($maxSamplesPerOrder);
    }

    /**
     * @When I set its sample product code prefix to :sampleProductCodePrefix
     */
    public function iSetItsSampleProductCodePrefixTo(string $sampleProductCodePrefix): void
    {
        $this->updatePage->setSampleProductCodePrefix($sampleProductCodePrefix);
    }

    /**
     * @Then /^(it|this channel) should allow (\d+) samples per order$/
     * @Then /^(channel "[^"]+") should allow (\d+) samples per order$/
     */
    public function itShouldAllowANumberOfSamplesPerOrder(ChannelInterface $channel, string $number): void
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::eq($this->updatePage->getMaxSamplesPerOrder(), $number);
    }

    /**
     * @Then /^(it|this channel) should have a sample product code prefix of "([^"]+)"$/
     * @Then /^(channel "[^"]+") should have a sample product code prefix of "([^"]+)"$/
     */
    public function itShouldHaveASampleProductCodePrefixOf(ChannelInterface $channel, string $prefix): void
    {
        $this->updatePage->open(['id' => $channel->getId()]);

        Assert::eq($this->updatePage->getSampleProductCodePrefix(), $prefix);
    }
}
