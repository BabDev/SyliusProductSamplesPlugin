<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Channel\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function setMaxSamplesPerOrder(string $maxSamplesPerOrder): void
    {
        $this->getElement('max_samples_per_order')->setValue($maxSamplesPerOrder);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'max_samples_per_order' => '#sylius_channel_maxSamplesPerOrder',
        ]);
    }
}
