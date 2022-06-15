<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Type;

use BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber\ManageSampleProductVariantAssignmentsFormSubscriber;
use BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber\SynchronizeSampleProductVariantTranslationsFormSubscriber;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class SampleProductVariantType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new ManageSampleProductVariantAssignmentsFormSubscriber())
            ->addEventSubscriber(new SynchronizeSampleProductVariantTranslationsFormSubscriber())
            ->add('shippingCategory', ShippingCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => 'sylius.ui.no_requirement',
                'label' => 'sylius.form.product_variant.shipping_category',
            ])
            ->add('width', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.width',
                'invalid_message' => 'sylius.product_variant.width.invalid',
            ])
            ->add('height', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.height',
                'invalid_message' => 'sylius.product_variant.height.invalid',
            ])
            ->add('depth', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.depth',
                'invalid_message' => 'sylius.product_variant.depth.invalid',
            ])
            ->add('weight', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.weight',
                'invalid_message' => 'sylius.product_variant.weight.invalid',
            ])
            ->add('taxCategory', TaxCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => '---',
                'label' => 'sylius.form.product_variant.tax_category',
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $event->getData();

            $event->getForm()->add('channelPricings', ChannelCollectionType::class, [
                'entry_type' => ChannelPricingType::class,
                'entry_options' => static fn (ChannelInterface $channel): array => [
                    'channel' => $channel,
                    'product_variant' => $productVariant,
                    'required' => false,
                ],
                'label' => 'sylius.form.variant.price',
            ]);
        });
    }
}
