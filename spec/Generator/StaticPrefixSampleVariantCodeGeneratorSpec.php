<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use PhpSpec\ObjectBehavior;

final class StaticPrefixSampleVariantCodeGeneratorSpec extends ObjectBehavior
{
    private const PREFIX = 'testing-prefix-';

    public function let(): void
    {
        $this->beConstructedWith(self::PREFIX);
    }

    public function it_generates_the_code_for_a_sample_product_variant(
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $variantCode = 'foo';

        $sampleVariant->getSampleOf()->willReturn($variant);

        $variant->getCode()->willReturn($variantCode);

        $this->generate($sampleVariant)->shouldReturn(self::PREFIX . $variantCode);
    }

    public function it_provides_the_prefix(): void
    {
        $this->getPrefix()->shouldReturn(self::PREFIX);
    }
}
