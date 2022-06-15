<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Model;

use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface
{
    /**
     * @phpstan-return positive-int|null
     */
    public function getMaxSamplesPerOrder(): ?int;

    /**
     * @phpstan-param positive-int|null $maxSamplesPerOrder
     */
    public function setMaxSamplesPerOrder(?int $maxSamplesPerOrder): void;

    public function getSampleProductCodePrefix(): ?string;

    public function setSampleProductCodePrefix(?string $sampleProductCodePrefix): void;
}
