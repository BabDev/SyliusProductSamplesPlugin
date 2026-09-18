<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use PhpSpec\ObjectBehavior;

final class TemplateSampleVariantCodeGeneratorSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('SAMPLE-{code}');
    }

    public function it_is_a_sample_variant_code_generator(): void
    {
        $this->shouldImplement(SampleVariantCodeGeneratorInterface::class);
    }

    public function it_prefixes_the_code_of_the_variant_the_sample_belongs_to(
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $sampleVariant->getSampleOf()->willReturn($variant);
        $variant->getCode()->willReturn('MUG-BLUE');

        $this->generate($sampleVariant)->shouldReturn('SAMPLE-MUG-BLUE');
    }

    public function it_suffixes_the_code_when_the_template_puts_the_placeholder_first(
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith('{code}-SAMPLE');

        $sampleVariant->getSampleOf()->willReturn($variant);
        $variant->getCode()->willReturn('MUG-BLUE');

        $this->generate($sampleVariant)->shouldReturn('MUG-BLUE-SAMPLE');
    }

    public function it_wraps_the_code_when_the_template_surrounds_the_placeholder(
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith('S-{code}-S');

        $sampleVariant->getSampleOf()->willReturn($variant);
        $variant->getCode()->willReturn('MUG-BLUE');

        $this->generate($sampleVariant)->shouldReturn('S-MUG-BLUE-S');
    }

    public function it_replaces_every_occurrence_of_the_placeholder(
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $this->beConstructedWith('{code}-SAMPLE-{code}');

        $sampleVariant->getSampleOf()->willReturn($variant);
        $variant->getCode()->willReturn('MUG-BLUE');

        $this->generate($sampleVariant)->shouldReturn('MUG-BLUE-SAMPLE-MUG-BLUE');
    }

    public function it_treats_a_variant_without_a_code_as_an_empty_string(
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $sampleVariant->getSampleOf()->willReturn($variant);
        $variant->getCode()->willReturn(null);

        $this->generate($sampleVariant)->shouldReturn('SAMPLE-');
    }
}
