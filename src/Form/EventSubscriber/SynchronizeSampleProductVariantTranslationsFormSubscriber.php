<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class SynchronizeSampleProductVariantTranslationsFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::SUBMIT => ['onSubmit', -10],
        ];
    }

    /**
     * @note This listener *MUST* run after {@see ManageSampleProductVariantAssignmentsFormSubscriber::onSubmit()} to ensure
     *       the relationships between the variant and its sample have been set
     */
    public function onSubmit(FormEvent $event): void
    {
        /** @var ProductVariantInterface|null $sampleVariant */
        $sampleVariant = $event->getData();

        if (null === $sampleVariant) {
            return;
        }

        Assert::isInstanceOf($sampleVariant, ProductVariantInterface::class);

        $actualVariant = $sampleVariant->getSampleOf();

        // First, remove outdated translations from the sample
        /** @var TranslationInterface $translation */
        foreach ($sampleVariant->getTranslations() as $translation) {
            if (!$actualVariant->hasTranslation($translation)) {
                $sampleVariant->removeTranslation($translation);
            }
        }

        /** @var TranslationInterface $translation */
        foreach ($actualVariant->getTranslations() as $translation) {
            if (!$sampleVariant->hasTranslation($translation)) {
                // Use the getter because the trait will create the appropriate model and set the relations
                $sampleVariant->getTranslation($translation->getLocale());
            }
        }
    }
}
