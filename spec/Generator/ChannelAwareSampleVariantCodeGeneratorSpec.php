<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;

final class ChannelAwareSampleVariantCodeGeneratorSpec extends ObjectBehavior
{
    private const PREFIX = 'testing-prefix-';

    public function let(ChannelContextInterface $channelContext, SampleVariantCodeGeneratorInterface $decoratedGenerator): void
    {
        $this->beConstructedWith($channelContext, $decoratedGenerator);
    }

    public function it_generates_the_code_for_a_sample_product_variant_when_the_channel_has_a_configured_prefix(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $variantCode = 'foo';

        $channelContext->getChannel()->willReturn($channel);

        $sampleVariant->getSampleOf()->willReturn($variant);

        $channel->getSampleProductCodePrefix()->willReturn(self::PREFIX);
        $variant->getCode()->willReturn($variantCode);

        $this->generate($sampleVariant)->shouldReturn(self::PREFIX . $variantCode);
    }

    public function it_generates_the_code_for_a_sample_product_variant_when_the_channel_does_not_have_a_configured_prefix(
        SampleVariantCodeGeneratorInterface $decoratedGenerator,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $code = 'decorated';

        $channelContext->getChannel()->willReturn($channel);

        $sampleVariant->getSampleOf()->willReturn($variant);

        $channel->getSampleProductCodePrefix()->willReturn(null);

        $decoratedGenerator->generate($sampleVariant)->willReturn($code);

        $this->generate($sampleVariant)->shouldReturn($code);
    }

    public function it_provides_the_prefix_when_the_channel_has_a_configured_prefix(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $channel->getSampleProductCodePrefix()->willReturn(self::PREFIX);

        $this->getPrefix()->shouldReturn(self::PREFIX);
    }

    public function it_provides_the_prefix_when_the_channel_does_not_have_a_configured_prefix(
        SampleVariantCodeGeneratorInterface $decoratedGenerator,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
    ): void {
        $prefix = 'decorated';

        $channelContext->getChannel()->willReturn($channel);

        $channel->getSampleProductCodePrefix()->willReturn(null);

        $decoratedGenerator->getPrefix()->willReturn($prefix);

        $this->getPrefix()->shouldReturn($prefix);
    }

    public function it_falls_back_to_the_decorated_generator_when_no_channel_can_be_resolved(
        ChannelContextInterface $channelContext,
        SampleVariantCodeGeneratorInterface $decoratedGenerator,
        ProductVariantInterface $sampleVariant,
    ): void {
        $channelContext->getChannel()->willThrow(new ChannelNotFoundException());

        $decoratedGenerator->generate($sampleVariant)->willReturn('SAMPLE-MUG-BLUE');

        $this->generate($sampleVariant)->shouldReturn('SAMPLE-MUG-BLUE');
    }

    public function it_falls_back_to_the_decorated_prefix_when_no_channel_can_be_resolved(
        ChannelContextInterface $channelContext,
        SampleVariantCodeGeneratorInterface $decoratedGenerator,
    ): void {
        $channelContext->getChannel()->willThrow(new ChannelNotFoundException());

        $decoratedGenerator->getPrefix()->willReturn('SAMPLE-');

        $this->getPrefix()->shouldReturn('SAMPLE-');
    }

    public function it_falls_back_to_the_decorated_generator_when_the_channel_is_not_sample_aware(
        ChannelContextInterface $channelContext,
        SampleVariantCodeGeneratorInterface $decoratedGenerator,
        BaseChannelInterface $channel,
        ProductVariantInterface $sampleVariant,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $decoratedGenerator->generate($sampleVariant)->willReturn('SAMPLE-MUG-BLUE');

        $this->generate($sampleVariant)->shouldReturn('SAMPLE-MUG-BLUE');
    }
}
