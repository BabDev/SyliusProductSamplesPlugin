<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface as CoreProductInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;

final class AddToCartTypeExtensionSpec extends ObjectBehavior
{
    public function it_does_not_add_the_button_for_a_product_which_is_not_sample_aware(
        FormBuilderInterface $builder,
        CoreProductInterface $product,
    ): void {
        $builder->add(Argument::cetera())->shouldNotBeCalled();
        $builder->addEventListener(Argument::cetera())->shouldNotBeCalled();

        $this->buildForm($builder, ['product' => $product]);
    }

    public function it_does_not_add_the_button_when_samples_are_not_active(
        FormBuilderInterface $builder,
        ProductInterface $product,
    ): void {
        $product->getSamplesActive()->willReturn(false);

        $builder->add(Argument::cetera())->shouldNotBeCalled();
        $builder->addEventListener(Argument::cetera())->shouldNotBeCalled();

        $this->buildForm($builder, ['product' => $product]);
    }

    public function it_swaps_the_cart_item_variant_for_its_sample_when_a_sample_was_requested(
        FormBuilderInterface $builder,
        ProductInterface $product,
        FormEvent $event,
        FormInterface $form,
        SubmitButton $button,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $cartItem,
        ProductVariantInterface $variant,
        ProductVariantInterface $sample,
    ): void {
        $listener = $this->captureSubmitListener($builder, $product);

        $button->isClicked()->willReturn(true);
        $form->get('requestSample')->willReturn($button);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($addToCartCommand);

        $addToCartCommand->getCartItem()->willReturn($cartItem);
        $cartItem->getVariant()->willReturn($variant);
        $variant->getSample()->willReturn($sample);

        $cartItem->setVariant($sample)->shouldBeCalled();

        $listener($event->getWrappedObject());
    }

    public function it_leaves_the_cart_item_alone_when_a_sample_was_not_requested(
        FormBuilderInterface $builder,
        ProductInterface $product,
        FormEvent $event,
        FormInterface $form,
        SubmitButton $button,
        OrderItemInterface $cartItem,
    ): void {
        $listener = $this->captureSubmitListener($builder, $product);

        $button->isClicked()->willReturn(false);
        $form->get('requestSample')->willReturn($button);

        $event->getForm()->willReturn($form);

        $cartItem->setVariant(Argument::any())->shouldNotBeCalled();

        $listener($event->getWrappedObject());
    }

    public function it_leaves_the_cart_item_alone_when_the_variant_has_no_sample(
        FormBuilderInterface $builder,
        ProductInterface $product,
        FormEvent $event,
        FormInterface $form,
        SubmitButton $button,
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $cartItem,
        ProductVariantInterface $variant,
    ): void {
        $listener = $this->captureSubmitListener($builder, $product);

        $button->isClicked()->willReturn(true);
        $form->get('requestSample')->willReturn($button);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($addToCartCommand);

        $addToCartCommand->getCartItem()->willReturn($cartItem);
        $cartItem->getVariant()->willReturn($variant);
        $variant->getSample()->willReturn(null);

        $cartItem->setVariant(Argument::any())->shouldNotBeCalled();

        $listener($event->getWrappedObject());
    }

    /**
     * Builds the form for a product with samples active and hands back the registered SUBMIT listener.
     */
    private function captureSubmitListener(FormBuilderInterface $builder, ProductInterface $product): callable
    {
        $listener = null;

        $product->getSamplesActive()->willReturn(true);

        $builder->add('requestSample', SubmitType::class, Argument::type('array'))->willReturn($builder);

        $builder->addEventListener(FormEvents::SUBMIT, Argument::type(\Closure::class))
            ->will(function (array $args, ObjectProphecy $object) use (&$listener): object {
                $listener = $args[1];

                return $object->reveal();
            })
        ;

        $this->buildForm($builder, ['product' => $product]);

        if (!\is_callable($listener)) {
            throw new \RuntimeException('The extension did not register a SUBMIT listener.');
        }

        return $listener;
    }
}
