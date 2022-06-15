<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Webmozart\Assert\Assert;

final class ChannelAwareSampleVariantCodeGenerator implements SampleVariantCodeGeneratorInterface
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private SampleVariantCodeGeneratorInterface $decoratedGenerator,
    ) {
    }

    public function generate(ProductVariantInterface $sampleVariant): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        Assert::isInstanceOf($channel, ChannelInterface::class);

        /** @var ProductVariantInterface|null $variant */
        $variant = $sampleVariant->getSampleOf();

        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        if (null === $channel->getSampleProductCodePrefix() || '' === trim($channel->getSampleProductCodePrefix())) {
            return $this->decoratedGenerator->generate($sampleVariant);
        }

        return $channel->getSampleProductCodePrefix() . ($variant->getCode() ?? '');
    }

    public function getPrefix(): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        Assert::isInstanceOf($channel, ChannelInterface::class);

        if (null === $channel->getSampleProductCodePrefix() || '' === trim($channel->getSampleProductCodePrefix())) {
            return $this->decoratedGenerator->getPrefix();
        }

        return $channel->getSampleProductCodePrefix();
    }
}
