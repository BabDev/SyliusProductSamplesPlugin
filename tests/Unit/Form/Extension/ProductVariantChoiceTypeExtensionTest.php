<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Unit\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Form\Extension\ProductVariantChoiceTypeExtension;
use BabDev\SyliusProductSamplesPlugin\Model\Product;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariant;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Component\Product\Model\Product as BaseProduct;
use Sylius\Component\Product\Model\ProductVariant as BaseProductVariant;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductVariantChoiceTypeExtensionTest extends TestCase
{
    /**
     * @test
     */
    public function it_extends_the_product_variant_choice_type(): void
    {
        self::assertSame(
            [ProductVariantChoiceType::class],
            [...ProductVariantChoiceTypeExtension::getExtendedTypes()],
        );
    }

    /**
     * @test
     */
    public function sample_variants_are_not_offered_as_choices(): void
    {
        $product = new Product();
        $product->setCode('MUG');

        $variant = new ProductVariant();
        $variant->setCode('MUG-BLUE');
        $product->addVariant($variant);

        $sample = new ProductVariant();
        $sample->setCode('SAMPLE-MUG-BLUE');
        $sample->setSampleOf($variant);
        $product->addVariant($sample);

        $choices = $this->resolveChoices($product);

        self::assertSame(['MUG-BLUE'], $this->codesOf($choices));
    }

    /**
     * @test
     */
    public function every_variant_is_offered_for_a_product_which_is_not_sample_aware(): void
    {
        $product = new BaseProduct();
        $product->setCode('MUG');

        foreach (['MUG-BLUE', 'MUG-RED'] as $code) {
            $variant = new BaseProductVariant();
            $variant->setCode($code);
            $product->addVariant($variant);
        }

        $choices = $this->resolveChoices($product);

        self::assertSame(['MUG-BLUE', 'MUG-RED'], $this->codesOf($choices));
    }

    /**
     * Resolves the options exactly as the form factory would: the type's defaults first, then the
     * extension's override on top.
     *
     * @return iterable<BaseProductVariant>
     */
    private function resolveChoices(object $product): iterable
    {
        $resolver = new OptionsResolver();

        (new ProductVariantChoiceType())->configureOptions($resolver);
        (new ProductVariantChoiceTypeExtension())->configureOptions($resolver);

        /** @var iterable<BaseProductVariant> $choices */
        $choices = $resolver->resolve(['product' => $product])['choices'];

        return $choices;
    }

    /**
     * @param iterable<BaseProductVariant> $variants
     *
     * @return list<string|null>
     */
    private function codesOf(iterable $variants): array
    {
        $codes = [];

        foreach ($variants as $variant) {
            $codes[] = $variant->getCode();
        }

        return $codes;
    }
}
