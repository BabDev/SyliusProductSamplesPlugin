<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Checker;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class SampleAwareProductVariantsParityCheckerSpec extends ObjectBehavior
{
    public function let(ProductVariantsParityCheckerInterface $decoratedChecker): void
    {
        $this->beConstructedWith($decoratedChecker);
    }

    public function it_is_a_product_variants_parity_checker(): void
    {
        $this->shouldImplement(ProductVariantsParityCheckerInterface::class);
    }

    public function it_delegates_for_a_product_which_does_not_support_samples(
        ProductVariantsParityCheckerInterface $decoratedChecker,
        BaseProductInterface $product,
        ProductVariantInterface $variant,
    ): void {
        $decoratedChecker->checkParity($variant, $product)->willReturn(true);

        $this->checkParity($variant, $product)->shouldBe(true);
    }

    public function it_does_not_check_a_sample_variant(
        ProductVariantsParityCheckerInterface $decoratedChecker,
        ProductInterface $product,
        ProductVariantInterface $variant,
        ProductVariantInterface $sample,
    ): void {
        $sample->getSampleOf()->willReturn($variant);

        $this->checkParity($sample, $product)->shouldBe(false);

        $decoratedChecker->checkParity(Argument::cetera())->shouldNotHaveBeenCalled();
        $product->getNonSampleVariants()->shouldNotHaveBeenCalled();
    }

    public function it_does_not_report_a_variant_as_colliding_with_its_own_sample(
        ProductInterface $product,
        ProductVariantInterface $blackTShirt,
        ProductVariantInterface $sampleBlackTShirt,
        ProductOptionInterface $colorOption,
        ProductOptionValueInterface $black,
    ): void {
        $blackTShirt->getSampleOf()->willReturn(null);
        $blackTShirt->getOptionValues()->willReturn(new ArrayCollection([$black->getWrappedObject()]));

        $product->getOptions()->willReturn(new ArrayCollection([$colorOption->getWrappedObject()]));

        /*
         * The sample carries the same option value as the variant it samples, so it would match; it is only
         * excluded because getNonSampleVariants() keeps it out of the candidate set.
         */
        $product->getNonSampleVariants()->willReturn(new ArrayCollection([$blackTShirt->getWrappedObject()]));

        $this->checkParity($blackTShirt, $product)->shouldBe(false);
    }

    public function it_reports_a_variant_colliding_with_another_non_sample_variant(
        ProductInterface $product,
        ProductVariantInterface $blackTShirt,
        ProductVariantInterface $anotherBlackTShirt,
        ProductOptionInterface $colorOption,
        ProductOptionValueInterface $black,
    ): void {
        $blackTShirt->getSampleOf()->willReturn(null);
        $blackTShirt->getOptionValues()->willReturn(new ArrayCollection([$black->getWrappedObject()]));

        $anotherBlackTShirt->hasOptionValue($black)->willReturn(true);

        $product->getOptions()->willReturn(new ArrayCollection([$colorOption->getWrappedObject()]));
        $product->getNonSampleVariants()->willReturn(
            new ArrayCollection([$blackTShirt->getWrappedObject(), $anotherBlackTShirt->getWrappedObject()]),
        );

        $this->checkParity($blackTShirt, $product)->shouldBe(true);
    }

    public function it_does_not_report_a_variant_with_a_different_option_set(
        ProductInterface $product,
        ProductVariantInterface $blackTShirt,
        ProductVariantInterface $whiteTShirt,
        ProductOptionInterface $colorOption,
        ProductOptionValueInterface $black,
    ): void {
        $blackTShirt->getSampleOf()->willReturn(null);
        $blackTShirt->getOptionValues()->willReturn(new ArrayCollection([$black->getWrappedObject()]));

        $whiteTShirt->hasOptionValue($black)->willReturn(false);

        $product->getOptions()->willReturn(new ArrayCollection([$colorOption->getWrappedObject()]));
        $product->getNonSampleVariants()->willReturn(
            new ArrayCollection([$blackTShirt->getWrappedObject(), $whiteTShirt->getWrappedObject()]),
        );

        $this->checkParity($blackTShirt, $product)->shouldBe(false);
    }
}
