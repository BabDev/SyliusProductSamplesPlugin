<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

/**
 * Drops sample variants from a product's serialized variant list for shop API consumers.
 *
 * @psalm-suppress PropertyNotSetInConstructor the normalizer is injected by NormalizerAwareTrait
 */
final class SampleAwareProductNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'babdev_sylius_product_samples_product_normalizer_already_called';

    public function __construct(
        private IriConverterInterface $iriConverter,
        private UserContextInterface $userContext,
    ) {
    }

    /**
     * @param mixed $object
     * @param string|null $format
     * @param array<array-key, mixed> $context
     *
     * @phpstan-return \ArrayObject<array-key, mixed>|array<array-key, mixed>|bool|float|int|string|null
     * @psalm-return \ArrayObject|array<array-key, mixed>|bool|float|int|string|null
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);

        if (!\is_array($data) || !isset($data['variants']) || !\is_array($data['variants'])) {
            return $data;
        }

        $user = $this->userContext->getUser();

        if (null !== $user && \in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $data;
        }

        /** @var ProductInterface $object */
        $sampleIris = [];

        foreach ($object->getVariants() as $variant) {
            if ($variant instanceof ProductVariantInterface && null !== $variant->getSampleOf()) {
                $sampleIris[] = $this->iriConverter->getIriFromResource($variant);
            }
        }

        if ([] === $sampleIris) {
            return $data;
        }

        $data['variants'] = array_values(array_filter(
            $data['variants'],
            // The list is normally a list of IRIs, but the same property can be embedded as objects when a consumer widens the serialization groups, so both shapes are matched.
            static function (mixed $entry) use ($sampleIris): bool {
                if (\is_string($entry)) {
                    return !\in_array($entry, $sampleIris, true);
                }

                if (\is_array($entry) && isset($entry['@id']) && \is_string($entry['@id'])) {
                    return !\in_array($entry['@id'], $sampleIris, true);
                }

                return true;
            },
        ));

        return $data;
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<array-key, mixed> $context
     */
    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ProductInterface;
    }
}
