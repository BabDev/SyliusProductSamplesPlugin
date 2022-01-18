<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

trait ProductTrait
{
    protected bool $samplesActive = false;

    public function getSamplesActive(): bool
    {
        return $this->samplesActive;
    }

    public function setSamplesActive(bool $samplesActive): void
    {
        $this->samplesActive = $samplesActive;
    }
}
