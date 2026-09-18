<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber\EnsureSampleVariantsHaveValidCodesFormSubscriber;
use BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber\ManageSampleProductVariantAssignmentsFormSubscriber;
use BabDev\SyliusProductSamplesPlugin\Form\Type\SampleProductVariantType;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    /**
     * Runs ahead of the listener {@see SampleProductVariantType} registers at the default priority,
     * so that type sees the sample variant when it builds the channel pricing fields.
     */
    private const PROVIDE_SAMPLE_PRIORITY = 10;

    public function __construct(
        private ProductVariantFactoryInterface $productVariantFactory,
        private SampleVariantCodeGeneratorInterface $codeGenerator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new EnsureSampleVariantsHaveValidCodesFormSubscriber($this->codeGenerator))
            ->add('sample', SampleProductVariantType::class)
        ;

        /*
         * The sample is handed to the sub-form rather than assigned to the variant, because this also
         * runs when the form is only being rendered. The assignment happens when the form is submitted instead.
         */
        $builder->get('sample')->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                if (null !== $event->getData()) {
                    return;
                }

                $variant = $event->getForm()->getParent()?->getData();

                if (!$variant instanceof ProductVariantInterface) {
                    return;
                }

                $product = $variant->getProduct();

                if (null === $product) {
                    return;
                }

                $sample = $this->productVariantFactory->createForProduct($product);

                if (!$sample instanceof ProductVariantInterface) {
                    return;
                }

                $sample->setSampleOf($variant);

                $event->setData($sample);
            },
            self::PROVIDE_SAMPLE_PRIORITY,
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductVariantType::class];
    }
}
