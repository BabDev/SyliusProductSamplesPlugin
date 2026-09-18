<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber\EnsureSampleVariantsHaveValidCodesFormSubscriber;
use BabDev\SyliusProductSamplesPlugin\Form\Type\SampleProductVariantType;
use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ProductVariantTypeExtensionSpec extends ObjectBehavior
{
    /**
     * Must stay ahead of the listener SampleProductVariantType registers at the default priority.
     */
    private const SAMPLE_LISTENER_PRIORITY = 10;

    public function let(
        ProductVariantFactoryInterface $productVariantFactory,
        SampleVariantCodeGeneratorInterface $codeGenerator,
    ): void {
        $this->beConstructedWith($productVariantFactory, $codeGenerator);
    }

    public function it_gives_the_sample_form_a_new_sample_without_assigning_it_to_the_variant(
        ProductVariantFactoryInterface $productVariantFactory,
        FormBuilderInterface $builder,
        FormBuilderInterface $sampleBuilder,
        FormEvent $event,
        FormInterface $sampleForm,
        FormInterface $variantForm,
        ProductInterface $product,
        ProductVariantInterface $variant,
        ProductVariantInterface $sample,
    ): void {
        $listener = $this->captureSampleListener($builder, $sampleBuilder);

        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($sampleForm);

        $sampleForm->getParent()->willReturn($variantForm);
        $variantForm->getData()->willReturn($variant);

        $variant->getProduct()->willReturn($product);

        $productVariantFactory->createForProduct($product)->willReturn($sample);

        $sample->setSampleOf($variant)->shouldBeCalled();

        // The whole point of the listener: the variant and its product are left alone while rendering
        $variant->setSample(Argument::any())->shouldNotBeCalled();
        $product->addVariant(Argument::any())->shouldNotBeCalled();

        $event->setData($sample)->shouldBeCalled();

        $listener($event->getWrappedObject());
    }

    public function it_leaves_an_existing_sample_alone(
        ProductVariantFactoryInterface $productVariantFactory,
        FormBuilderInterface $builder,
        FormBuilderInterface $sampleBuilder,
        FormEvent $event,
        ProductVariantInterface $existingSample,
    ): void {
        $listener = $this->captureSampleListener($builder, $sampleBuilder);

        $event->getData()->willReturn($existingSample);

        $productVariantFactory->createForProduct(Argument::any())->shouldNotBeCalled();
        $event->setData(Argument::any())->shouldNotBeCalled();

        $listener($event->getWrappedObject());
    }

    public function it_does_nothing_when_the_variant_is_not_sample_aware(
        ProductVariantFactoryInterface $productVariantFactory,
        FormBuilderInterface $builder,
        FormBuilderInterface $sampleBuilder,
        FormEvent $event,
        FormInterface $sampleForm,
        FormInterface $variantForm,
    ): void {
        $listener = $this->captureSampleListener($builder, $sampleBuilder);

        $event->getData()->willReturn(null);
        $event->getForm()->willReturn($sampleForm);

        $sampleForm->getParent()->willReturn($variantForm);
        $variantForm->getData()->willReturn(new \stdClass());

        $productVariantFactory->createForProduct(Argument::any())->shouldNotBeCalled();
        $event->setData(Argument::any())->shouldNotBeCalled();

        $listener($event->getWrappedObject());
    }

    /**
     * Builds the form and hands back the PRE_SET_DATA listener registered on the sample sub-form.
     *
     * The priority is part of the contract, not an implementation detail: SampleProductVariantType
     * registers its own PRE_SET_DATA listener at the default priority and reads the sample to build
     * its channel pricing fields, so this one has to run first. Nothing else observes that ordering
     * cheaply, which is why it is asserted here.
     */
    private function captureSampleListener(FormBuilderInterface $builder, FormBuilderInterface $sampleBuilder): callable
    {
        $listener = null;

        $builder->addEventSubscriber(Argument::type(EnsureSampleVariantsHaveValidCodesFormSubscriber::class))->willReturn($builder);
        $builder->add('sample', SampleProductVariantType::class)->willReturn($builder);
        $builder->get('sample')->willReturn($sampleBuilder);

        $sampleBuilder->addEventListener(FormEvents::PRE_SET_DATA, Argument::type(\Closure::class), self::SAMPLE_LISTENER_PRIORITY)
            ->will(function (array $args) use (&$listener): void {
                $listener = $args[1];
            })
        ;

        $this->buildForm($builder, []);

        if (!\is_callable($listener)) {
            throw new \RuntimeException(sprintf(
                'The extension did not register a PRE_SET_DATA listener on the sample sub-form at priority %d.',
                self::SAMPLE_LISTENER_PRIORITY,
            ));
        }

        return $listener;
    }
}
