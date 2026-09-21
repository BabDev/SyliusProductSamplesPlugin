<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductGenerateVariantsType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantGenerationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Keeps sample variants out of the admin variant generation form.
 */
final class ProductGenerateVariantsTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('variants', CollectionType::class, [
                'entry_type' => ProductVariantGenerationType::class,
                'property_path' => 'nonSampleVariants',
                'allow_add' => false,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductGenerateVariantsType::class];
    }
}
