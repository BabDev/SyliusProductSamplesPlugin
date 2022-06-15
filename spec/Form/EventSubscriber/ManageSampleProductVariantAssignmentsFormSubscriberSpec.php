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

final class ManageSampleProductVariantAssignmentsFormSubscriberSpec extends ObjectBehavior
{
    public function it_clears_the_form_data_when_creating_a_product_and_samples_are_not_active(
        FormEvent $event,
        FormInterface $form,
        FormInterface $variantForm,
        FormInterface $productForm,
        FormInterface $samplesActiveForm,
        ProductVariantInterface $sampleVariant,
        ProductInterface $product,
    ): void {
        $form->getParent()->willReturn($variantForm);

        $variantForm->getParent()->willReturn($productForm);

        $productForm->getData()->willReturn($product);
        $productForm->get('samplesActive')->willReturn($samplesActiveForm);

        $product->removeVariant($sampleVariant)->shouldBeCalled();

        $samplesActiveForm->getData()->willReturn(false);

        $sampleVariant->getId()->willReturn(null);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($sampleVariant);
        $event->setData(null)->shouldBeCalled();

        $this->onSubmit($event);
    }

    public function it_sets_the_sample_variant_relations_when_creating_a_product_and_samples_are_active(
        FormEvent $event,
        FormInterface $form,
        FormInterface $variantForm,
        FormInterface $productForm,
        FormInterface $samplesActiveForm,
        ProductVariantInterface $variant,
        ProductVariantInterface $sampleVariant,
    ): void {
        $form->getParent()->willReturn($variantForm);

        $variantForm->getParent()->willReturn($productForm);
        $variantForm->getData()->willReturn($variant);

        $productForm->get('samplesActive')->willReturn($samplesActiveForm);

        $samplesActiveForm->getData()->willReturn(true);

        $sampleVariant->getId()->willReturn(null);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($sampleVariant);

        $variant->setSample($sampleVariant)->shouldBeCalled();

        $sampleVariant->setSampleOf($variant)->shouldBeCalled();

        $this->onSubmit($event);
    }

    public function it_does_nothing_when_updating_a_product_and_samples_are_active(
        FormEvent $event,
        FormInterface $form,
        FormInterface $variantForm,
        FormInterface $productForm,
        FormInterface $samplesActiveForm,
        ProductVariantInterface $variant,
        ProductVariantInterface $sampleVariant,
    ): void {
        $form->getParent()->willReturn($variantForm);

        $variantForm->getParent()->willReturn($productForm);
        $variantForm->getData()->willReturn($variant);

        $productForm->get('samplesActive')->willReturn($samplesActiveForm);

        $samplesActiveForm->getData()->willReturn(true);

        $sampleVariant->getId()->willReturn(42);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($sampleVariant);

        $variant->setSample($sampleVariant)->shouldNotBeCalled();

        $sampleVariant->setSampleOf($variant)->shouldNotBeCalled();

        $this->onSubmit($event);
    }

    public function it_clears_the_form_data_when_creating_a_product_variant_and_samples_are_not_active(
        FormEvent $event,
        FormInterface $form,
        FormInterface $variantForm,
        ProductVariantInterface $sampleVariant,
        ProductInterface $product,
    ): void {
        $form->getParent()->willReturn($variantForm);

        $variantForm->getParent()->willReturn(null);

        $sampleVariant->getProduct()->willReturn($product);
        $sampleVariant->getId()->willReturn(null);

        $product->getSamplesActive()->willReturn(false);
        $product->removeVariant($sampleVariant)->shouldBeCalled();

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($sampleVariant);
        $event->setData(null)->shouldBeCalled();

        $this->onSubmit($event);
    }

    public function it_sets_the_sample_variant_relations_when_creating_a_product_variant_and_samples_are_active(
        FormEvent $event,
        FormInterface $form,
        FormInterface $variantForm,
        ProductVariantInterface $variant,
        ProductVariantInterface $sampleVariant,
        ProductInterface $product,
    ): void {
        $form->getParent()->willReturn($variantForm);

        $variantForm->getParent()->willReturn(null);
        $variantForm->getData()->willReturn($variant);

        $sampleVariant->getProduct()->willReturn($product);
        $sampleVariant->getId()->willReturn(null);

        $product->getSamplesActive()->willReturn(true);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($sampleVariant);

        $variant->setSample($sampleVariant)->shouldBeCalled();

        $sampleVariant->setSampleOf($variant)->shouldBeCalled();

        $this->onSubmit($event);
    }

    public function it_does_nothing_when_updating_a_product_variant_and_samples_are_active(
        FormEvent $event,
        FormInterface $form,
        FormInterface $variantForm,
        ProductVariantInterface $variant,
        ProductVariantInterface $sampleVariant,
        ProductInterface $product,
    ): void {
        $form->getParent()->willReturn($variantForm);

        $variantForm->getParent()->willReturn(null);

        $sampleVariant->getProduct()->willReturn($product);
        $sampleVariant->getId()->willReturn(42);

        $product->getSamplesActive()->willReturn(true);

        $event->getForm()->willReturn($form);
        $event->getData()->willReturn($sampleVariant);

        $variant->setSample($sampleVariant)->shouldNotBeCalled();

        $sampleVariant->setSampleOf($variant)->shouldNotBeCalled();

        $this->onSubmit($event);
    }
}
