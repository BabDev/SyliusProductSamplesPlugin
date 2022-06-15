<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
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
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    /**
     * @note We don't handle setting the variant code in this method because this listener is called before the form data
     *       for the parent variant form has been merged onto the ProductVariant model, instead that will be handled by
     *       {@see EnsureSampleVariantsHaveValidCodesFormSubscriber} on the root form
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
                /** @var ProductInterface $product */
                $product = $productForm->getData();
                $product->removeVariant($sampleVariant);

                $event->setData(null);

                return;
            }
        } else {
            if (!$sampleVariant->getProduct()->getSamplesActive() && null === $sampleVariant->getId()) {
                $sampleVariant->getProduct()->removeVariant($sampleVariant);

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
