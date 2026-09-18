<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Webmozart\Assert\Assert;

/**
 * Prefixes a sample variant's code with the prefix configured on the active channel.
 *
 * A variant has a single sample whose code is globally unique, so the prefix is a naming convention
 * applied at the moment the sample is created, not a per-channel value: the code does not change if
 * the product is later sold through a channel with a different prefix. Whenever a channel prefix
 * cannot be resolved the decorated generator is used, which covers a store whose admin is not served
 * from a channel's hostname, a store with several channels and no matching hostname, and a store
 * whose channel model does not implement this plugin's interface.
 */
final class ChannelAwareSampleVariantCodeGenerator implements SampleVariantCodeGeneratorInterface
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private SampleVariantCodeGeneratorInterface $decoratedGenerator,
    ) {
    }

    public function generate(ProductVariantInterface $sampleVariant): string
    {
        $prefix = $this->resolveChannelPrefix();

        if (null === $prefix) {
            return $this->decoratedGenerator->generate($sampleVariant);
        }

        /** @var ProductVariantInterface|null $variant */
        $variant = $sampleVariant->getSampleOf();

        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        return $prefix . ($variant->getCode() ?? '');
    }

    public function getPrefix(): string
    {
        return $this->resolveChannelPrefix() ?? $this->decoratedGenerator->getPrefix();
    }

    private function resolveChannelPrefix(): ?string
    {
        try {
            $channel = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException) {
            return null;
        }

        if (!$channel instanceof ChannelInterface) {
            return null;
        }

        $prefix = $channel->getSampleProductCodePrefix();

        if (null === $prefix || '' === trim($prefix)) {
            return null;
        }

        return $prefix;
    }
}
