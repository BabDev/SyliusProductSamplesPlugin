<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * @todo The code prefix should be configurable
 */
final class StaticPrefixSampleVariantCodeGenerator implements SampleVariantCodeGeneratorInterface
{
    public function __construct(private string $prefix = 'SAMPLE-')
    {
    }

    public function generate(ProductVariantInterface $sampleVariant): string
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $sampleVariant->getSampleOf();

        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        return $this->prefix . ($variant->getCode() ?? '');
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
