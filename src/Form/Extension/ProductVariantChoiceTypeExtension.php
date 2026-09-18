<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Component\Product\Model\ProductInterface as BaseProductInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Keeps sample variants out of the variant choices a customer is offered.
 */
final class ProductVariantChoiceTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('choices', static function (Options $options): iterable {
            $product = $options['product'];

            if ($product instanceof ProductInterface) {
                return $product->getNonSampleVariants();
            }

            return $product instanceof BaseProductInterface ? $product->getVariants() : [];
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductVariantChoiceType::class];
    }
}
