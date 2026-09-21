<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Setup;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    public function __construct(
        private ObjectManager $objectManager,
        private ChannelContextInterface $channelContext,
    ) {
    }

    /**
     * @Given /^the store allows (\d+) samples? per order$/
     */
    public function theStoreAllowsSamplesPerOrder(int $limit): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $this->setMaxSamplesPerOrder($channel, $limit);
    }

    /**
     * @Given /^the (channel "[^"]+") allows (\d+) samples? per order$/
     */
    public function theChannelAllowsSamplesPerOrder(ChannelInterface $channel, int $limit): void
    {
        $this->setMaxSamplesPerOrder($channel, $limit);
    }

    /**
     * @Given /^the store does not limit how many samples an order may contain$/
     */
    public function theStoreDoesNotLimitSamplesPerOrder(): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $channel->setMaxSamplesPerOrder(null);

        $this->objectManager->flush();
    }

    private function setMaxSamplesPerOrder(ChannelInterface $channel, int $limit): void
    {
        // The channel form rejects anything below 1, so a scenario asking for a lower limit is a broken scenario
        Assert::positiveInteger($limit);

        $channel->setMaxSamplesPerOrder($limit);

        $this->objectManager->flush();
    }
}
