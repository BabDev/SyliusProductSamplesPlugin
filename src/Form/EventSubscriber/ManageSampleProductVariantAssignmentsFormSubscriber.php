<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class ManageSampleProductVariantAssignmentsFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::SUBMIT => ['onSubmit', 0],
        ];
    }

    /**
     * @note We don't handle setting the variant code in this method because this listener is called before the form data
     *       for the parent variant form has been merged onto the ProductVariant model, instead that will be handled by
     *       {@see EnsureSampleVariantsHaveValidCodesFormSubscriber} on the root form
     * @note The sample is only ever attached to its variant here, on submit. It is not part of the product's variant
     *       collection at this point; it reaches the database through the cascade on the variant's `sample` association
     */
    public function onSubmit(FormEvent $event): void
    {
        /** @var ProductVariantInterface $sampleVariant */
        $sampleVariant = $event->getData();

        Assert::isInstanceOf($sampleVariant, ProductVariantInterface::class);

        $variantForm = $event->getForm()->getParent();

        /*
         * If we are editing a product, the variant form will have a parent and we'll want to check the form data.
         * If we're editing a single variant, then we can check the product data directly.
         */
        if (null !== $productForm = $variantForm->getParent()) {
            if (!$productForm->get('samplesActive')->getData() && null === $sampleVariant->getId()) {
                $event->setData(null);

                return;
            }
        } else {
            if (!$sampleVariant->getProduct()->getSamplesActive() && null === $sampleVariant->getId()) {
                $event->setData(null);

                return;
            }
        }

        if (null !== $sampleVariant->getId()) {
            return;
        }

        /** @var ProductVariantInterface $actualVariant */
        $actualVariant = $variantForm->getData();
        $actualVariant->setSample($sampleVariant);

        $sampleVariant->setSampleOf($actualVariant);
    }
}
