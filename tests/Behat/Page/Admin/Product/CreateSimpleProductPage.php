<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\CreateSimpleProductPage as BaseCreatePage;

class CreateSimpleProductPage extends BaseCreatePage implements CreateSimpleProductPageInterface
{
    public function enableProductSamples(): void
    {
        $this->getElement('samples_active')->check();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'samples_active' => '#sylius_product_samplesActive',
        ]);
    }
}
