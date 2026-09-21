<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Unit\Validator\Constraints;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use BabDev\SyliusProductSamplesPlugin\Validator\Constraints\MaxSamplesPerOrder;
use BabDev\SyliusProductSamplesPlugin\Validator\Constraints\MaxSamplesPerOrderValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @extends ConstraintValidatorTestCase<MaxSamplesPerOrderValidator>
 */
final class MaxSamplesPerOrderValidatorTest extends ConstraintValidatorTestCase
{
    private const MESSAGE = 'babdev_sylius_product_samples.order.allowed_samples.max_per_order';

    /**
     * @test
     */
    public function no_violation_is_raised_when_there_is_nothing_to_validate(): void
    {
        $this->validator->validate(null, new MaxSamplesPerOrder());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_channel_does_not_limit_samples(): void
    {
        $this->validator->validate($this->createOrder(null), new MaxSamplesPerOrder());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_channel_limit_is_zero(): void
    {
        $order = $this->createOrder(0, $this->createItem($this->createSampleVariant(), 5));

        $this->validator->validate($order, new MaxSamplesPerOrder());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_order_is_within_the_limit(): void
    {
        $order = $this->createOrder(2, $this->createItem($this->createSampleVariant(), 2));

        $this->validator->validate($order, new MaxSamplesPerOrder());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function non_sample_items_do_not_count_toward_the_limit(): void
    {
        $order = $this->createOrder(
            1,
            $this->createItem($this->createSampleVariant(), 1),
            $this->createItem($this->createVariant(), 10),
        );

        $this->validator->validate($order, new MaxSamplesPerOrder());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function a_violation_is_raised_when_the_order_exceeds_the_limit(): void
    {
        $order = $this->createOrder(2, $this->createItem($this->createSampleVariant(), 3));

        $this->validator->validate($order, new MaxSamplesPerOrder());

        $this->buildViolation(self::MESSAGE)
            ->setParameter('{{ limit }}', '2')
            ->setPlural(2)
            ->assertRaised()
        ;
    }

    /**
     * @test
     */
    public function the_quantity_of_every_sample_item_is_counted(): void
    {
        $order = $this->createOrder(
            3,
            $this->createItem($this->createSampleVariant(), 2),
            $this->createItem($this->createSampleVariant(), 2),
        );

        $this->validator->validate($order, new MaxSamplesPerOrder());

        $this->buildViolation(self::MESSAGE)
            ->setParameter('{{ limit }}', '3')
            ->setPlural(3)
            ->assertRaised()
        ;
    }

    /**
     * @test
     */
    public function the_pending_item_is_counted_on_top_of_the_cart_for_an_add_to_cart_command(): void
    {
        $command = $this->createAddToCartCommand(
            $this->createOrder(2, $this->createItem($this->createSampleVariant(), 2)),
            $this->createItem($this->createSampleVariant(), 1),
        );

        $this->validator->validate($command, new MaxSamplesPerOrder());

        $this->buildViolation(self::MESSAGE)
            ->setParameter('{{ limit }}', '2')
            ->setPlural(2)
            ->assertRaised()
        ;
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_pending_item_keeps_the_cart_within_the_limit(): void
    {
        $command = $this->createAddToCartCommand(
            $this->createOrder(3, $this->createItem($this->createSampleVariant(), 2)),
            $this->createItem($this->createSampleVariant(), 1),
        );

        $this->validator->validate($command, new MaxSamplesPerOrder());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_pending_item_is_not_a_sample(): void
    {
        $command = $this->createAddToCartCommand(
            $this->createOrder(1),
            $this->createItem($this->createVariant(), 10),
        );

        $this->validator->validate($command, new MaxSamplesPerOrder());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function an_unsupported_value_is_rejected(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new MaxSamplesPerOrder());
    }

    /**
     * @test
     */
    public function an_unsupported_constraint_is_rejected(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate($this->createOrder(1), new NotNull());
    }

    /** @var MockObject&OrderRepositoryInterface */
    private MockObject $orderRepository;

    /** @var MockObject&ProductVariantRepositoryInterface */
    private MockObject $productVariantRepository;

    protected function createValidator(): MaxSamplesPerOrderValidator
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);

        return new MaxSamplesPerOrderValidator($this->orderRepository, $this->productVariantRepository);
    }

    /**
     * Builds the command the shop API validates, wiring the repositories it resolves the cart and the
     * variant through.
     */
    private function createAddItemToCartCommand(
        ?OrderInterface $cart,
        ?ProductVariantInterface $variant,
        int $quantity,
    ): AddItemToCart {
        $command = AddItemToCart::createFromData('TOKEN', 'VARIANT_CODE', $quantity);

        $this->orderRepository->method('findCartByTokenValue')->with('TOKEN')->willReturn($cart);
        $this->productVariantRepository->method('findOneBy')->with(['code' => 'VARIANT_CODE'])->willReturn($variant);

        return $command;
    }

    private function createOrder(?int $maxSamplesPerOrder, OrderItemInterface ...$items): OrderInterface
    {
        /** @var MockObject&ChannelInterface $channel */
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getMaxSamplesPerOrder')->willReturn($maxSamplesPerOrder);

        /** @var MockObject&OrderInterface $order */
        $order = $this->createMock(OrderInterface::class);
        $order->method('getChannel')->willReturn($channel);
        $order->method('getItems')->willReturn(new ArrayCollection($items));

        return $order;
    }

    private function createAddToCartCommand(OrderInterface $cart, OrderItemInterface $cartItem): AddToCartCommandInterface
    {
        /** @var MockObject&AddToCartCommandInterface $command */
        $command = $this->createMock(AddToCartCommandInterface::class);
        $command->method('getCart')->willReturn($cart);
        $command->method('getCartItem')->willReturn($cartItem);

        return $command;
    }

    private function createItem(ProductVariantInterface $variant, int $quantity): OrderItemInterface
    {
        /** @var MockObject&OrderItemInterface $item */
        $item = $this->createMock(OrderItemInterface::class);
        $item->method('getVariant')->willReturn($variant);
        $item->method('getQuantity')->willReturn($quantity);

        return $item;
    }

    private function createVariant(): ProductVariantInterface
    {
        /** @var MockObject&ProductVariantInterface $variant */
        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('getSampleOf')->willReturn(null);

        return $variant;
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_api_adds_a_sample_within_the_limit(): void
    {
        $sampleVariant = $this->createSampleVariant();
        $order = $this->createOrder(2, $this->createItem($sampleVariant, 1));

        $this->validator->validate(
            $this->createAddItemToCartCommand($order, $sampleVariant, 1),
            new MaxSamplesPerOrder(),
        );

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function a_violation_is_raised_when_the_api_adds_a_sample_beyond_the_limit(): void
    {
        $sampleVariant = $this->createSampleVariant();
        $order = $this->createOrder(1, $this->createItem($sampleVariant, 1));

        $this->validator->validate(
            $this->createAddItemToCartCommand($order, $sampleVariant, 1),
            new MaxSamplesPerOrder(),
        );

        $this->buildViolation(self::MESSAGE)
            ->setParameter('{{ limit }}', '1')
            ->setPlural(1)
            ->assertRaised()
        ;
    }

    /**
     * @test
     */
    public function a_violation_is_raised_when_the_api_adds_more_samples_at_once_than_the_limit_allows(): void
    {
        $sampleVariant = $this->createSampleVariant();
        $order = $this->createOrder(2);

        $this->validator->validate(
            $this->createAddItemToCartCommand($order, $sampleVariant, 3),
            new MaxSamplesPerOrder(),
        );

        $this->buildViolation(self::MESSAGE)
            ->setParameter('{{ limit }}', '2')
            ->setPlural(2)
            ->assertRaised()
        ;
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_api_adds_a_variant_which_is_not_a_sample(): void
    {
        $order = $this->createOrder(1, $this->createItem($this->createSampleVariant(), 1));

        $this->validator->validate(
            $this->createAddItemToCartCommand($order, $this->createVariant(), 5),
            new MaxSamplesPerOrder(),
        );

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function no_violation_is_raised_when_the_api_names_a_cart_which_cannot_be_found(): void
    {
        $this->validator->validate(
            $this->createAddItemToCartCommand(null, $this->createSampleVariant(), 1),
            new MaxSamplesPerOrder(),
        );

        $this->assertNoViolation();
    }

    private function createSampleVariant(): ProductVariantInterface
    {
        /** @var MockObject&ProductVariantInterface $sampleVariant */
        $sampleVariant = $this->createMock(ProductVariantInterface::class);
        $sampleVariant->method('getSampleOf')->willReturn($this->createVariant());

        return $sampleVariant;
    }
}
