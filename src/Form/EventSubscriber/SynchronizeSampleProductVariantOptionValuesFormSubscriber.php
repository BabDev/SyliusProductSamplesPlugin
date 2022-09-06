<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantOptionValuesSynchronizerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class SynchronizeSampleProductVariantOptionValuesFormSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ProductVariantOptionValuesSynchronizerInterface $optionValuesSynchronizer,
    ) {
    }

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

        $this->optionValuesSynchronizer->synchronize($sampleVariant);
    }
}
