<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Setup;

use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    public function __construct(
        private ObjectManager $objectManager,
        private ProductVariantFactoryInterface $productVariantFactory,
    ) {
    }

    /**
     * @Given /^(this product) has product samples enabled$/
     * @Given /^the ("([^"]*)" product) has product samples enabled$/
     */
    public function theProductHasSamplesEnabled(ProductInterface $product): void
    {
        $product->setSamplesActive(true);

        foreach ($product->getVariants() as $variant) {
            Assert::isInstanceOf($variant, ProductVariantInterface::class);

            if (null === $variant->getSample()) {
                $sample = $this->productVariantFactory->createForProduct($product);
                $sample->setCode(sprintf('SAMPLE-%s', $variant->getCode() ?? ''));

                $variant->setSample($sample);
                $product->addVariant($sample);
            }
        }

        $this->objectManager->flush();
    }
}
