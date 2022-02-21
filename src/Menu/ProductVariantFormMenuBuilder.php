<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\ProductVariantMenuBuilderEvent;

final class ProductVariantFormMenuBuilder
{
    public function addProductSamplesMenu(ProductVariantMenuBuilderEvent $event): void
    {
        $event->getMenu()
            ->addChild('product_samples')
            ->setAttribute('template', '@BabDevSyliusProductSamplesPlugin/Admin/ProductVariant/Tab/_product_samples.html.twig')
            ->setLabel('babdev_sylius_product_samples.ui.product_samples')
        ;
    }
}
