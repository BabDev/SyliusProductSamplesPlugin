<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPage as BaseUpdatePage;

class UpdateSimpleProductPage extends BaseUpdatePage implements UpdateSimpleProductPageInterface
{
    public function disableProductSamples(): void
    {
        $this->getElement('samples_active')->uncheck();
    }

    public function enableProductSamples(): void
    {
        $this->getElement('samples_active')->check();
    }

    public function hasProductSamplesEnabled(): bool
    {
        return $this->getElement('samples_active')->isChecked();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'samples_active' => '#sylius_product_samplesActive',
        ]);
    }
}
