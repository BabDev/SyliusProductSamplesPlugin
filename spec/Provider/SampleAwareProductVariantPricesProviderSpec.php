<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Provider;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class SampleAwareProductVariantPricesProviderSpec extends ObjectBehavior
{
    public function let(ProductVariantPricesCalculatorInterface $productVariantPricesCalculator): void
    {
        $this->beConstructedWith($productVariantPricesCalculator);
    }

    public function it_is_a_variants_prices_provider(): void
    {
        $this->shouldImplement(ProductVariantsPricesProviderInterface::class);
    }

    public function it_provides_the_product_variant_options_map_with_corresponding_prices_when_samples_are_active(
        ChannelInterface $channel,
        ProductInterface $tShirt,
        ProductOptionValueInterface $black,
        ProductOptionValueInterface $large,
        ProductOptionValueInterface $small,
        ProductOptionValueInterface $white,
        ProductVariantInterface $blackLargeTShirt,
        ProductVariantInterface $blackSmallTShirt,
        ProductVariantInterface $whiteLargeTShirt,
        ProductVariantInterface $whiteSmallTShirt,
        ProductVariantInterface $sampleBlackLargeTShirt,
        ProductVariantInterface $sampleBlackSmallTShirt,
        ProductVariantInterface $sampleWhiteLargeTShirt,
        ProductVariantInterface $sampleWhiteSmallTShirt,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator
    ): void {
        $tShirt->getEnabledVariants()->willReturn(new ArrayCollection([
            $blackSmallTShirt->getWrappedObject(),
            $whiteSmallTShirt->getWrappedObject(),
            $blackLargeTShirt->getWrappedObject(),
            $whiteLargeTShirt->getWrappedObject(),
        ]));

        $tShirt->getSamplesActive()->willReturn(true);

        $blackSmallTShirt->getProduct()->willReturn($tShirt);
        $whiteSmallTShirt->getProduct()->willReturn($tShirt);
        $blackLargeTShirt->getProduct()->willReturn($tShirt);
        $whiteLargeTShirt->getProduct()->willReturn($tShirt);

        $blackSmallTShirt->getSample()->willReturn($sampleBlackSmallTShirt);
        $whiteSmallTShirt->getSample()->willReturn($sampleWhiteSmallTShirt);
        $blackLargeTShirt->getSample()->willReturn($sampleBlackLargeTShirt);
        $whiteLargeTShirt->getSample()->willReturn($sampleWhiteLargeTShirt);

        $blackSmallTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $whiteSmallTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $blackLargeTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $whiteLargeTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $blackSmallTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$black->getWrappedObject(), $small->getWrappedObject()])
        );
        $whiteSmallTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$white->getWrappedObject(), $small->getWrappedObject()])
        );
        $blackLargeTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$black->getWrappedObject(), $large->getWrappedObject()])
        );
        $whiteLargeTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$white->getWrappedObject(), $large->getWrappedObject()])
        );

        $productVariantPricesCalculator->calculate($blackSmallTShirt, ['channel' => $channel])->willReturn(1000);
        $productVariantPricesCalculator->calculateOriginal($blackSmallTShirt, ['channel' => $channel])->willReturn(1000);
        $productVariantPricesCalculator->calculate($whiteSmallTShirt, ['channel' => $channel])->willReturn(1500);
        $productVariantPricesCalculator->calculateOriginal($whiteSmallTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPricesCalculator->calculate($blackLargeTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPricesCalculator->calculateOriginal($blackLargeTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPricesCalculator->calculate($whiteLargeTShirt, ['channel' => $channel])->willReturn(2500);
        $productVariantPricesCalculator->calculateOriginal($whiteLargeTShirt, ['channel' => $channel])->willReturn(3000);
        $productVariantPricesCalculator->calculate($sampleBlackSmallTShirt, ['channel' => $channel])->willReturn(0);
        $productVariantPricesCalculator->calculate($sampleWhiteSmallTShirt, ['channel' => $channel])->willReturn(0);
        $productVariantPricesCalculator->calculate($sampleBlackLargeTShirt, ['channel' => $channel])->willReturn(0);
        $productVariantPricesCalculator->calculate($sampleWhiteLargeTShirt, ['channel' => $channel])->willReturn(0);

        $black->getOptionCode()->willReturn('t_shirt_color');
        $white->getOptionCode()->willReturn('t_shirt_color');
        $small->getOptionCode()->willReturn('t_shirt_size');
        $large->getOptionCode()->willReturn('t_shirt_size');

        $black->getCode()->willReturn('black');
        $white->getCode()->willReturn('white');
        $small->getCode()->willReturn('small');
        $large->getCode()->willReturn('large');

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn([
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'small',
                'value' => 1000,
                'sample-price' => 0,
                'free-sample' => 'yes',
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'small',
                'value' => 1500,
                'original-price' => 2000,
                'sample-price' => 0,
                'free-sample' => 'yes',
            ],
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'large',
                'value' => 2000,
                'sample-price' => 0,
                'free-sample' => 'yes',
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'large',
                'value' => 2500,
                'original-price' => 3000,
                'sample-price' => 0,
                'free-sample' => 'yes',
            ],
        ]);
    }

    public function it_provides_the_product_variant_options_map_with_corresponding_prices_when_samples_are_not_active(
        ChannelInterface $channel,
        ProductInterface $tShirt,
        ProductOptionValueInterface $black,
        ProductOptionValueInterface $large,
        ProductOptionValueInterface $small,
        ProductOptionValueInterface $white,
        ProductVariantInterface $blackLargeTShirt,
        ProductVariantInterface $blackSmallTShirt,
        ProductVariantInterface $whiteLargeTShirt,
        ProductVariantInterface $whiteSmallTShirt,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator
    ): void {
        $tShirt->getEnabledVariants()->willReturn(new ArrayCollection([
            $blackSmallTShirt->getWrappedObject(),
            $whiteSmallTShirt->getWrappedObject(),
            $blackLargeTShirt->getWrappedObject(),
            $whiteLargeTShirt->getWrappedObject(),
        ]));

        $tShirt->getSamplesActive()->willReturn(false);

        $blackSmallTShirt->getProduct()->willReturn($tShirt);
        $whiteSmallTShirt->getProduct()->willReturn($tShirt);
        $blackLargeTShirt->getProduct()->willReturn($tShirt);
        $whiteLargeTShirt->getProduct()->willReturn($tShirt);

        $blackSmallTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $whiteSmallTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $blackLargeTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $whiteLargeTShirt->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());

        $blackSmallTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$black->getWrappedObject(), $small->getWrappedObject()])
        );
        $whiteSmallTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$white->getWrappedObject(), $small->getWrappedObject()])
        );
        $blackLargeTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$black->getWrappedObject(), $large->getWrappedObject()])
        );
        $whiteLargeTShirt->getOptionValues()->willReturn(
            new ArrayCollection([$white->getWrappedObject(), $large->getWrappedObject()])
        );

        $productVariantPricesCalculator->calculate($blackSmallTShirt, ['channel' => $channel])->willReturn(1000);
        $productVariantPricesCalculator->calculateOriginal($blackSmallTShirt, ['channel' => $channel])->willReturn(1000);
        $productVariantPricesCalculator->calculate($whiteSmallTShirt, ['channel' => $channel])->willReturn(1500);
        $productVariantPricesCalculator->calculateOriginal($whiteSmallTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPricesCalculator->calculate($blackLargeTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPricesCalculator->calculateOriginal($blackLargeTShirt, ['channel' => $channel])->willReturn(2000);
        $productVariantPricesCalculator->calculate($whiteLargeTShirt, ['channel' => $channel])->willReturn(2500);
        $productVariantPricesCalculator->calculateOriginal($whiteLargeTShirt, ['channel' => $channel])->willReturn(3000);

        $black->getOptionCode()->willReturn('t_shirt_color');
        $white->getOptionCode()->willReturn('t_shirt_color');
        $small->getOptionCode()->willReturn('t_shirt_size');
        $large->getOptionCode()->willReturn('t_shirt_size');

        $black->getCode()->willReturn('black');
        $white->getCode()->willReturn('white');
        $small->getCode()->willReturn('small');
        $large->getCode()->willReturn('large');

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn([
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'small',
                'value' => 1000,
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'small',
                'value' => 1500,
                'original-price' => 2000,
            ],
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'large',
                'value' => 2000,
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'large',
                'value' => 2500,
                'original-price' => 3000,
            ],
        ]);
    }
}
