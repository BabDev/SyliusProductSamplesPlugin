<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $product = $options['product'];

        if (!$product instanceof ProductInterface || !$product->getSamplesActive()) {
            return;
        }

        $builder
            ->add('requestSample', SubmitType::class, [
                'label' => 'babdev_sylius_product_samples.ui.request_a_sample',
            ])
        ;

        /*
         * This listener runs on submit to handle mapping the sample variant to the care before the
         * validation listener runs on post-submit, allowing the validator to see the correct data.
         */
        $builder->addEventListener(FormEvents::SUBMIT, static function (FormEvent $event): void {
            $button = $event->getForm()->get('requestSample');

            if (!$button instanceof ClickableInterface || !$button->isClicked()) {
                return;
            }

            $addToCartCommand = $event->getData();

            if (!$addToCartCommand instanceof AddToCartCommandInterface) {
                return;
            }

            $cartItem = $addToCartCommand->getCartItem();

            if (!$cartItem instanceof OrderItemInterface) {
                return;
            }

            $variant = $cartItem->getVariant();

            if (!$variant instanceof ProductVariantInterface) {
                return;
            }

            $sample = $variant->getSample();

            // A variant can be missing its sample when samples were activated after the variant was created
            if (!$sample instanceof ProductVariantInterface) {
                return;
            }

            $cartItem->setVariant($sample);
        });
    }

    public static function getExtendedTypes(): iterable
    {
        return [AddToCartType::class];
    }
}
