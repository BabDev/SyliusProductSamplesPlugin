<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslatedPrefixSampleVariantNameGeneratorSpec extends ObjectBehavior
{
    public function let(TranslatorInterface $translator): void
    {
        $this->beConstructedWith($translator);
    }

    public function it_generates_the_name_for_a_sample_product_variant_without_a_locale(
        TranslatorInterface $translator,
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
    ): void {
        $variantName = 'Product';
        $prefixedVariantName = 'Sample - Product';

        $sampleVariant->getSampleOf()->willReturn($variant);

        $variant->getName()->willReturn($variantName);

        $translator->trans('babdev_sylius_product_samples.product_variant.prefixed_sample_name', ['%name%' => $variantName], null, null)->willReturn($prefixedVariantName);

        $this->generate($sampleVariant)->shouldReturn($prefixedVariantName);
    }

    public function it_generates_the_name_for_a_sample_product_variant_with_a_locale(
        TranslatorInterface $translator,
        ProductVariantInterface $sampleVariant,
        ProductVariantInterface $variant,
        ProductVariantTranslationInterface $variantTranslation,
    ): void {
        $locale = 'en';

        $variantName = 'Product';
        $prefixedVariantName = 'Sample - Product';

        $sampleVariant->getSampleOf()->willReturn($variant);

        $variant->getTranslation($locale)->willReturn($variantTranslation);

        $variantTranslation->getName()->willReturn($variantName);

        $translator->trans('babdev_sylius_product_samples.product_variant.prefixed_sample_name', ['%name%' => $variantName], null, $locale)->willReturn($prefixedVariantName);

        $this->generate($sampleVariant, $locale)->shouldReturn($prefixedVariantName);
    }
}
