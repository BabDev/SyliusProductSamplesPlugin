<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

use Sylius\Component\Core\Model\ProductInterface as BaseProductInterface;

interface ProductInterface extends BaseProductInterface
{
    public function getSamplesActive(): bool;

    public function setSamplesActive(bool $samplesActive): void;
}
