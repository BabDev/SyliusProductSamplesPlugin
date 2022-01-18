<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Menu;

use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;

final class ProductFormMenuBuilder
{
    public function addProductSamplesMenu(ProductMenuBuilderEvent $event): void
    {
        $event->getMenu()
            ->addChild('product_samples')
            ->setAttribute('template', '@BabDevSyliusProductSamplesPlugin/Admin/Product/Tab/_product_samples.html.twig')
            ->setLabel('babdev_sylius_product_samples.ui.product_samples')
        ;
    }
}
