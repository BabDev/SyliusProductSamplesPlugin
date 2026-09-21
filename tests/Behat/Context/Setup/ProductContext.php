<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Context\Setup;

use BabDev\SyliusProductSamplesPlugin\EventListener\SampleVariantGeneratorListener;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;

final class ProductContext implements Context
{
    public function __construct(
        private ObjectManager $objectManager,
        private SampleVariantGeneratorListener $sampleVariantGenerator,
    ) {
    }

    /**
     * @Given /^(this product) has product samples enabled for all channels$/
     * @Given /^the ("([^"]*)" product) has product samples enabled for all channels$/
     */
    public function theProductHasSamplesEnabledForAllChannels(ProductInterface $product): void
    {
        $this->enableSamplesForAllChannelsWithPrice($product, 0);
    }

    /**
     * @Given /^(this product) has product samples enabled for all channels priced at ("[^"]+")$/
     * @Given /^the ("([^"]*)" product) has product samples enabled for all channels priced at ("[^"]+")$/
     */
    public function theProductHasSamplesEnabledForAllChannelsAtPrice(ProductInterface $product, int $price): void
    {
        $this->enableSamplesForAllChannelsWithPrice($product, $price);
    }

    private function enableSamplesForAllChannelsWithPrice(ProductInterface $product, int $price): void
    {
        $product->setSamplesActive(true);

        $this->sampleVariantGenerator->ensureSampleVariantsExist(new ResourceControllerEvent($product));

        if (0 !== $price) {
            foreach ($product->getNonSampleVariants() as $variant) {
                $sample = $variant->getSample();

                if (!$sample instanceof ProductVariantInterface) {
                    continue;
                }

                foreach ($sample->getChannelPricings() as $channelPricing) {
                    $channelPricing->setPrice($price);
                }
            }
        }

        $this->objectManager->flush();
    }
}
