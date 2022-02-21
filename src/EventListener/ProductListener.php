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
        /** @var ProductInterface $product */
        $product = $event->getSubject();

        foreach ($product->getVariants() as $variant) {
            if (!$variant instanceof ProductVariantInterface) {
                continue;
            }

            if (null === $sample = $variant->getSample()) {
                continue;
            }

            if (null === $sample->getCode()) {
                $sample->setCode(sprintf('SAMPLE-%s', $variant->getCode() ?? ''));
            }
        }
    }
}
