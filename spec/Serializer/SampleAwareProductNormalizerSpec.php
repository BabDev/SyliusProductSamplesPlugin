<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SampleAwareProductNormalizerSpec extends ObjectBehavior
{
    public function let(
        IriConverterInterface $iriConverter,
        UserContextInterface $userContext,
        NormalizerInterface $normalizer,
    ): void {
        $this->beConstructedWith($iriConverter, $userContext);
        $this->setNormalizer($normalizer);
    }

    public function it_is_a_context_aware_normalizer(): void
    {
        $this->shouldImplement(ContextAwareNormalizerInterface::class);
    }

    public function it_supports_a_sample_aware_product(ProductInterface $product): void
    {
        $this->supportsNormalization($product)->shouldBe(true);
    }

    public function it_does_not_support_a_product_it_has_already_normalized(ProductInterface $product): void
    {
        $this->supportsNormalization($product, null, ['babdev_sylius_product_samples_product_normalizer_already_called' => true])
            ->shouldBe(false)
        ;
    }

    public function it_removes_sample_variants_from_the_serialized_variants(
        IriConverterInterface $iriConverter,
        UserContextInterface $userContext,
        NormalizerInterface $normalizer,
        ProductInterface $mug,
        ProductVariantInterface $blueMug,
        ProductVariantInterface $blueMugSample,
    ): void {
        $userContext->getUser()->willReturn(null);

        $blueMug->getSampleOf()->willReturn(null);
        $blueMugSample->getSampleOf()->willReturn($blueMug);

        $mug->getVariants()->willReturn(new ArrayCollection([
            $blueMug->getWrappedObject(),
            $blueMugSample->getWrappedObject(),
        ]));

        $iriConverter->getIriFromResource($blueMugSample)->willReturn('/api/v2/shop/product-variants/SAMPLE-MUG-BLUE');

        $normalizer->normalize($mug, null, Argument::any())->willReturn([
            'code' => 'MUG',
            'variants' => [
                '/api/v2/shop/product-variants/MUG-BLUE',
                '/api/v2/shop/product-variants/SAMPLE-MUG-BLUE',
            ],
        ]);

        $this->normalize($mug)->shouldBe([
            'code' => 'MUG',
            'variants' => ['/api/v2/shop/product-variants/MUG-BLUE'],
        ]);
    }

    public function it_leaves_the_variants_alone_for_an_admin_consumer(
        UserContextInterface $userContext,
        NormalizerInterface $normalizer,
        ProductInterface $mug,
        UserInterface $user,
    ): void {
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);
        $userContext->getUser()->willReturn($user);

        $serialized = [
            'code' => 'MUG',
            'variants' => [
                '/api/v2/shop/product-variants/MUG-BLUE',
                '/api/v2/shop/product-variants/SAMPLE-MUG-BLUE',
            ],
        ];

        $normalizer->normalize($mug, null, Argument::any())->willReturn($serialized);

        $this->normalize($mug)->shouldBe($serialized);
    }

    public function it_leaves_a_payload_without_variants_alone(
        UserContextInterface $userContext,
        NormalizerInterface $normalizer,
        ProductInterface $mug,
    ): void {
        $userContext->getUser()->willReturn(null);

        $normalizer->normalize($mug, null, Argument::any())->willReturn(['code' => 'MUG']);

        $this->normalize($mug)->shouldBe(['code' => 'MUG']);
    }
}
