<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Form\Type\SampleProductVariantType;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    public function __construct(private ProductVariantFactoryInterface $productVariantFactory)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sample', SampleProductVariantType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $variant = $event->getData();

            Assert::isInstanceOf($variant, ProductVariantInterface::class);

            if (null === $variant->getSample()) {
                $sample = $this->productVariantFactory->createForProduct($variant->getProduct());
                $sample->setSampleOf($variant);

                $variant->setSample($sample);
                $variant->getProduct()->addVariant($sample);
            }
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductVariantType::class];
    }
}
