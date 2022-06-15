<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class EnsureSampleVariantsHaveValidCodesFormSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SampleVariantCodeGeneratorInterface $codeGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => ['postSubmit', 9],
        ];
    }

    public function postSubmit(FormEvent $event): void
    {
        if (!$event->getForm()->isRoot()) {
            return;
        }

        if ($event->getData() instanceof ProductInterface) {
            foreach ($event->getData()->getVariants() as $variant) {
                Assert::isInstanceOf($variant, ProductVariantInterface::class);

                $this->ensureSampleVariantHasCode($variant);
            }
        } elseif ($event->getData() instanceof ProductVariantInterface) {
            $this->ensureSampleVariantHasCode($event->getData());
        }
    }

    private function ensureSampleVariantHasCode(ProductVariantInterface $variant): void
    {
        if (null === $sample = $variant->getSample()) {
            return;
        }

        Assert::isInstanceOf($sample, ProductVariantInterface::class);

        if (null !== $sample->getCode() && $this->codeGenerator->getPrefix() !== $sample->getCode()) {
            return;
        }

        $sample->setCode($this->codeGenerator->generate($sample));
    }
}
