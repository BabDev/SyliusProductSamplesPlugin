<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class TranslatedPrefixSampleVariantNameGenerator implements SampleVariantNameGeneratorInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function generate(ProductVariantInterface $sampleVariant, ?string $locale = null): string
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $sampleVariant->getSampleOf();

        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $name = null !== $locale ? $variant->getTranslation($locale)->getName() : $variant->getName();

        return $this->translator->trans('babdev_sylius_product_samples.product_variant.prefixed_sample_name', ['%name%' => $name], null, $locale);
    }
}
