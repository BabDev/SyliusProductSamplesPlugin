<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\EventListener;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Webmozart\Assert\Assert;

/**
 * @todo The code prefix should be configurable
 */
final class SampleVariantCodeGeneratorListener
{
    public function ensureSampleVariantsHaveCodes(ResourceControllerEvent $event): void
    {
        if ($event->getSubject() instanceof ProductInterface) {
            foreach ($event->getSubject()->getVariants() as $variant) {
                Assert::isInstanceOf($variant, ProductVariantInterface::class);

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
