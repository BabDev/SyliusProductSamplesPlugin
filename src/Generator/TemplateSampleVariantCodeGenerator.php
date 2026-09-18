<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * Builds a sample variant's code by substituting the code of the variant it is a sample of into a configured template.
 */
final class TemplateSampleVariantCodeGenerator implements SampleVariantCodeGeneratorInterface
{
    /**
     * Replaced with the code of the variant the sample belongs to.
     */
    public const CODE_PLACEHOLDER = '{code}';

    public function __construct(private string $template)
    {
    }

    public function generate(ProductVariantInterface $sampleVariant): string
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $sampleVariant->getSampleOf();

        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        return str_replace(self::CODE_PLACEHOLDER, $variant->getCode() ?? '', $this->template);
    }
}
