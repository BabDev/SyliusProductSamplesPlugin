<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Menu;

use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;

final class ProductFormMenuBuilderSpec extends ObjectBehavior
{
    public function it_adds_the_menu_item_to_the_product_form_menu(ProductMenuBuilderEvent $event, ItemInterface $menu): void
    {
        $event->getMenu()->willReturn($menu);
        $menu->addChild('product_samples')->willReturn($menu);
        $menu->setAttribute('template', '@BabDevSyliusProductSamplesPlugin/Admin/Product/Tab/_product_samples.html.twig')->willReturn($menu);
        $menu->setLabel('babdev_sylius_product_samples.ui.product_samples')->willReturn($menu);
    }
}