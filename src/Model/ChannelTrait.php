<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

trait ChannelTrait
{
    /** @phpstan-var positive-int|null */
    protected ?int $maxSamplesPerOrder = null;

    protected ?string $sampleProductCodePrefix = null;

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

    public function getSampleProductCodePrefix(): ?string
    {
        return $this->sampleProductCodePrefix;
    }

    public function setSampleProductCodePrefix(?string $sampleProductCodePrefix): void
    {
        $this->sampleProductCodePrefix = $sampleProductCodePrefix;
    }
}
