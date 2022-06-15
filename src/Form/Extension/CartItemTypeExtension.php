<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantMatchType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CartItemTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($builder->has('variant')) {
            $builder->remove('variant');

            if (ProductInterface::VARIANT_SELECTION_CHOICE === $options['product']->getVariantSelectionMethod()) {
                $builder->add('variant', ProductVariantChoiceType::class, [
                    'choices' => $options['product']->getNonSampleVariants(),
                    'product' => $options['product'],
                ]);
            } else {
                $builder->add('variant', ProductVariantMatchType::class, [
                    'product' => $options['product'],
                ]);
            }
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [CartItemType::class];
    }
}
