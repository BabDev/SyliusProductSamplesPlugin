<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Provider;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface as CoreProductInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;

final class SampleAwareProductVariantPricesProviderSpec extends ObjectBehavior
{
    public function let(
        ProductVariantsPricesProviderInterface $decoratedProvider,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
    ): void {
        $this->beConstructedWith($decoratedProvider, $productVariantPricesCalculator);
    }

    public function it_is_a_variants_prices_provider(): void
    {
        $this->shouldImplement(ProductVariantsPricesProviderInterface::class);
    }

    public function it_adds_the_sample_pricing_to_the_decorated_providers_option_maps(
        ProductVariantsPricesProviderInterface $decoratedProvider,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductInterface $tShirt,
        ProductVariantInterface $blackTShirt,
        ProductVariantInterface $whiteTShirt,
        ProductVariantInterface $sampleBlackTShirt,
        ProductVariantInterface $sampleWhiteTShirt,
    ): void {
        $decoratedProvider->provideVariantsPrices($tShirt, $channel)->willReturn([
            ['t_shirt_color' => 'black', 'value' => 1000],
            ['t_shirt_color' => 'white', 'value' => 1500, 'original-price' => 2000],
        ]);

        $tShirt->getSamplesActive()->willReturn(true);
        $tShirt->getEnabledVariants()->willReturn(new ArrayCollection([
            $blackTShirt->getWrappedObject(),
            $whiteTShirt->getWrappedObject(),
        ]));

        $blackTShirt->getSample()->willReturn($sampleBlackTShirt);
        $whiteTShirt->getSample()->willReturn($sampleWhiteTShirt);

        $productVariantPricesCalculator->calculate($sampleBlackTShirt, ['channel' => $channel])->willReturn(0);
        $productVariantPricesCalculator->calculate($sampleWhiteTShirt, ['channel' => $channel])->willReturn(250);

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn([
            ['t_shirt_color' => 'black', 'value' => 1000, 'sample-price' => 0, 'free-sample' => 'yes'],
            ['t_shirt_color' => 'white', 'value' => 1500, 'original-price' => 2000, 'sample-price' => 250, 'free-sample' => 'no'],
        ]);
    }

    public function it_returns_the_decorated_providers_option_maps_untouched_when_samples_are_not_active(
        ProductVariantsPricesProviderInterface $decoratedProvider,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductInterface $tShirt,
    ): void {
        $prices = [['t_shirt_color' => 'black', 'value' => 1000]];

        $decoratedProvider->provideVariantsPrices($tShirt, $channel)->willReturn($prices);

        $tShirt->getSamplesActive()->willReturn(false);

        $productVariantPricesCalculator->calculate(Argument::cetera())->shouldNotBeCalled();

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn($prices);
    }

    public function it_returns_the_decorated_providers_option_maps_untouched_for_a_product_which_is_not_sample_aware(
        ProductVariantsPricesProviderInterface $decoratedProvider,
        ChannelInterface $channel,
        CoreProductInterface $tShirt,
    ): void {
        $prices = [['t_shirt_color' => 'black', 'value' => 1000]];

        $decoratedProvider->provideVariantsPrices($tShirt, $channel)->willReturn($prices);

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn($prices);
    }

    public function it_omits_the_sample_pricing_for_a_variant_which_has_no_sample(
        ProductVariantsPricesProviderInterface $decoratedProvider,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductInterface $tShirt,
        ProductVariantInterface $blackTShirt,
        ProductVariantInterface $whiteTShirt,
        ProductVariantInterface $sampleBlackTShirt,
    ): void {
        $decoratedProvider->provideVariantsPrices($tShirt, $channel)->willReturn([
            ['t_shirt_color' => 'black', 'value' => 1000],
            ['t_shirt_color' => 'white', 'value' => 1500],
        ]);

        $tShirt->getSamplesActive()->willReturn(true);
        $tShirt->getEnabledVariants()->willReturn(new ArrayCollection([
            $blackTShirt->getWrappedObject(),
            $whiteTShirt->getWrappedObject(),
        ]));

        $blackTShirt->getSample()->willReturn($sampleBlackTShirt);
        $whiteTShirt->getSample()->willReturn(null);

        $productVariantPricesCalculator->calculate($sampleBlackTShirt, ['channel' => $channel])->willReturn(0);

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn([
            ['t_shirt_color' => 'black', 'value' => 1000, 'sample-price' => 0, 'free-sample' => 'yes'],
            ['t_shirt_color' => 'white', 'value' => 1500],
        ]);
    }

    public function it_leaves_the_option_maps_alone_when_they_do_not_line_up_with_the_enabled_variants(
        ProductVariantsPricesProviderInterface $decoratedProvider,
        ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        ChannelInterface $channel,
        ProductInterface $tShirt,
        ProductVariantInterface $blackTShirt,
        ProductVariantInterface $whiteTShirt,
    ): void {
        $prices = [['t_shirt_color' => 'black', 'value' => 1000]];

        $decoratedProvider->provideVariantsPrices($tShirt, $channel)->willReturn($prices);

        $tShirt->getSamplesActive()->willReturn(true);
        $tShirt->getEnabledVariants()->willReturn(new ArrayCollection([
            $blackTShirt->getWrappedObject(),
            $whiteTShirt->getWrappedObject(),
        ]));

        $productVariantPricesCalculator->calculate(Argument::cetera())->shouldNotBeCalled();

        $this->provideVariantsPrices($tShirt, $channel)->shouldReturn($prices);
    }
}
