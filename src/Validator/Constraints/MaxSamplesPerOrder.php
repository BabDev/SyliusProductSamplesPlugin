<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class MaxSamplesPerOrder extends Constraint
{
    public string $message = 'babdev_sylius_product_samples.order.allowed_samples.max_per_order';

    public function validatedBy(): string
    {
        return 'babdev_sylius_product_samples_max_samples_per_order';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
