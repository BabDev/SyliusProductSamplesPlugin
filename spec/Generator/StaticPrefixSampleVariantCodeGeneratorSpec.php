<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Generator;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
}
