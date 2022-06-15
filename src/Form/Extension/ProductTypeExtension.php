<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber\EnsureSampleVariantsHaveValidCodesFormSubscriber;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private SampleVariantCodeGeneratorInterface $codeGenerator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new EnsureSampleVariantsHaveValidCodesFormSubscriber($this->codeGenerator))
            ->add('samplesActive', CheckboxType::class, [
                'required' => false,
                'label' => 'babdev_sylius_product_samples.form.product.samples_available',
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
