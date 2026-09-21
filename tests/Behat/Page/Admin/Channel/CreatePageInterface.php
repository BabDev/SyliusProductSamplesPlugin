<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Channel\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function setMaxSamplesPerOrder(string $maxSamplesPerOrder): void;
}
