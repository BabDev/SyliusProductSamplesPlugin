<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Validator\Constraints;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validates that an order does not contain more sample variants than its channel allows.
 */
final class MaxSamplesPerOrderValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MaxSamplesPerOrder) {
            throw new UnexpectedTypeException($constraint, MaxSamplesPerOrder::class);
        }

        if (null === $value) {
            return;
        }

        $pendingItem = null;

        if ($value instanceof AddToCartCommandInterface) {
            $order = $value->getCart();
            $pendingItem = $value->getCartItem();
        } elseif ($value instanceof OrderInterface) {
            $order = $value;
        } else {
            throw new UnexpectedValueException($value, OrderInterface::class . '|' . AddToCartCommandInterface::class);
        }

        if (!$order instanceof OrderInterface) {
            return;
        }

        $channel = $order->getChannel();

        if (!$channel instanceof ChannelInterface) {
            return;
        }

        $limit = $channel->getMaxSamplesPerOrder();

        if (null === $limit || $limit <= 0) {
            return;
        }

        $samples = 0;

        foreach ($order->getItems() as $item) {
            if ($this->isSample($item)) {
                $samples += $item->getQuantity();
            }
        }

        /*
         * The pending item is added on top of the cart's current contents rather than replacing a
         * matching item, because Sylius merges the quantities of equal items when the cart is processed.
         */
        if (null !== $pendingItem && $this->isSample($pendingItem)) {
            $samples += $pendingItem->getQuantity();
        }

        if ($samples <= $limit) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ limit }}', (string) $limit)
            ->setPlural($limit)
            ->addViolation()
        ;
    }

    private function isSample(mixed $item): bool
    {
        if (!$item instanceof OrderItemInterface) {
            return false;
        }

        $variant = $item->getVariant();

        return $variant instanceof ProductVariantInterface && null !== $variant->getSampleOf();
    }
}
