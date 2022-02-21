<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class ProductTypeExtension extends AbstractTypeExtension
{
    public function __construct(private ProductVariantFactoryInterface $productVariantFactory)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('samplesActive', CheckboxType::class, [
                'required' => false,
                'label' => 'babdev_sylius_product_samples.form.product.samples_available',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $product = $event->getData();

            Assert::isInstanceOf($product, ProductInterface::class);

            if (!$product->isSimple()) {
                return;
            }

            $variant = $product->getVariants()->first();

            Assert::isInstanceOf($variant, ProductVariantInterface::class);

            if (null === $variant->getSample()) {
                $variant->setSample($this->productVariantFactory->createForProduct($product));
            }
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [ProductType::class];
    }
}
