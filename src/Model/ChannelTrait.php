<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

trait ChannelTrait
{
    /** @phpstan-var positive-int|null */
    protected ?int $maxSamplesPerOrder = null;

    /**
     * @phpstan-return positive-int|null
     */
    public function getMaxSamplesPerOrder(): ?int
    {
        return $this->maxSamplesPerOrder;
    }

    /**
     * @phpstan-param positive-int|null $maxSamplesPerOrder
     */
    public function setMaxSamplesPerOrder(?int $maxSamplesPerOrder): void
    {
        $this->maxSamplesPerOrder = $maxSamplesPerOrder;
    }
}
