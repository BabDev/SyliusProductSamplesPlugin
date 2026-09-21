<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Validator\Constraints;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @psalm-suppress PropertyNotSetInConstructor the execution context is supplied by ConstraintValidator::initialize()
 */
final class MaxSamplesPerOrderValidator extends ConstraintValidator
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductVariantRepositoryInterface $productVariantRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MaxSamplesPerOrder) {
            throw new UnexpectedTypeException($constraint, MaxSamplesPerOrder::class);
        }

        if (null === $value) {
            return;
        }

        /*
         * Each entry point hands over a different shape. The form flow passes the order itself, or the
         * command carrying the cart item that is about to be added; the API passes a command naming the
         * cart and the variant by identifier, with nothing built yet. All three reduce to the order plus
         * the quantity of samples waiting to join it.
         */
        [$order, $pendingSamples] = match (true) {
            $value instanceof OrderInterface => [$value, 0],
            $value instanceof AddToCartCommandInterface => [$value->getCart(), $this->countPendingItem($value->getCartItem())],
            $value instanceof AddItemToCart => $this->resolveApiCommand($value),
            default => throw new UnexpectedValueException(
                $value,
                implode('|', [OrderInterface::class, AddToCartCommandInterface::class, AddItemToCart::class]),
            ),
        };

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
         * The pending samples are added on top of the cart's current contents rather than replacing a
         * matching item, because Sylius merges the quantities of equal items when the cart is processed.
         */
        $samples += $pendingSamples;

        if ($samples <= $limit) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ limit }}', (string) $limit)
            ->setPlural($limit)
            ->addViolation()
        ;
    }

    /**
     * @return array{0: OrderInterface|null, 1: int}
     */
    private function resolveApiCommand(AddItemToCart $command): array
    {
        $order = null === $command->orderTokenValue
            ? null
            : $this->orderRepository->findCartByTokenValue($command->orderTokenValue);

        if (!$order instanceof OrderInterface) {
            return [null, 0];
        }

        $variant = $this->productVariantRepository->findOneBy(['code' => $command->productVariantCode]);

        $pending = $variant instanceof ProductVariantInterface && null !== $variant->getSampleOf()
            ? $command->quantity
            : 0;

        return [$order, $pending];
    }

    /**
     * The cart item carried by the form command is only known to be an order item of the base contract,
     * so it is narrowed here rather than in the caller.
     */
    private function countPendingItem(mixed $item): int
    {
        if (!$item instanceof OrderItemInterface) {
            return 0;
        }

        return $this->isSample($item) ? $item->getQuantity() : 0;
    }

    private function isSample(OrderItemInterface $item): bool
    {
        $variant = $item->getVariant();

        return $variant instanceof ProductVariantInterface && null !== $variant->getSampleOf();
    }
}
