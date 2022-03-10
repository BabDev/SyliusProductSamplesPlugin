<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\EventListener;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class ProductListener
{
    public function ensureSampleVariantsHaveCodes(ResourceControllerEvent $event): void
    {
        if ($event->getSubject() instanceof ProductInterface) {
            foreach ($event->getSubject()->getVariants() as $variant) {
                if (!$variant instanceof ProductVariantInterface) {
                    continue;
                }

                $this->ensureSampleVariantHasCode($variant);
            }
        } elseif ($event->getSubject() instanceof ProductVariantInterface) {
            $this->ensureSampleVariantHasCode($event->getSubject());
        }
    }

    private function ensureSampleVariantHasCode(ProductVariantInterface $variant): void
    {
        if (null === $sample = $variant->getSample()) {
            return;
        }

        if (null !== $sample->getCode()) {
            return;
        }

        $sample->setCode(sprintf('SAMPLE-%s', $variant->getCode() ?? ''));
    }
}
