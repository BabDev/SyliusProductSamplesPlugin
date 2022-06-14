<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

final class EnsureSampleVariantsHaveValidCodesFormSubscriberSpec extends ObjectBehavior
{
    public function it_generates_the_code_for_sample_variants_after_submitting_the_product_form(
        FormEvent $event,
        FormInterface $form,
        ProductInterface $product,
        ProductVariantInterface $variant1,
        ProductVariantInterface $variant2,
        ProductVariantInterface $sampleOfVariant2,
    ): void {
        $form->isRoot()->willReturn(true);
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($product);

        $product->getVariants()->willReturn(new ArrayCollection([$variant1->getWrappedObject(), $variant2->getWrappedObject(), $sampleOfVariant2->getWrappedObject()]));

        /*
         * Variant 1
         */

        $variant1->getSample()->willReturn(null);

        /*
         * Variant 2
         */

        $variant2->getSample()->willReturn($sampleOfVariant2);
        $variant2->getCode()->willReturn('variant-2');
        $sampleOfVariant2->getCode()->willReturn(null);
        $sampleOfVariant2->setCode('SAMPLE-variant-2')->shouldBeCalled();

        /*
         * Sample of Variant 2
         */

        $sampleOfVariant2->getSample()->willReturn(null);

        $this->postSubmit($event);
    }

    public function it_generates_the_code_for_sample_variants_after_submitting_the_product_variant_form(
        FormEvent $event,
        FormInterface $form,
        ProductVariantInterface $variant,
        ProductVariantInterface $sampleOfVariant,
    ): void {
        $form->isRoot()->willReturn(true);
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($variant);

        $variant->getSample()->willReturn($sampleOfVariant);
        $variant->getCode()->willReturn('variant');
        $sampleOfVariant->getCode()->willReturn(null);
        $sampleOfVariant->setCode('SAMPLE-variant')->shouldBeCalled();

        $this->postSubmit($event);
    }

    public function it_does_not_generate_the_code_for_sample_variants_when_the_product_variant_form_is_a_child_form(
        FormEvent $event,
        FormInterface $form,
    ): void {
        $form->isRoot()->willReturn(false);
        $event->getForm()->willReturn($form);
        $event->getData()->shouldNotBeCalled();

        $this->postSubmit($event);
    }

    public function it_does_nothing_after_submitting_a_form_with_an_unsupported_data_type(
        FormEvent $event,
        FormInterface $form,
        ChannelInterface $channel,
        ProductVariantInterface $sampleOfVariant,
    ): void {
        $form->isRoot()->willReturn(true);
        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($channel);

        $this->postSubmit($event);
    }
}
