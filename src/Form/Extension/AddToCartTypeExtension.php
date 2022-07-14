<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\SubmitButton;
use Webmozart\Assert\Assert;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $product = $options['product'];

        if ($product instanceof ProductInterface && $product->getSamplesActive()) {
            $builder
                ->add('requestSample', SubmitType::class, [
                    'label' => 'babdev_sylius_product_samples.ui.request_a_sample',
                ])
            ;

            // This listener must run after the validation listener
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
                /** @var SubmitButton $button */
                $button = $event->getForm()->get('requestSample');

                if (!$button->isClicked()) {
                    return;
                }

                /** @var AddToCartCommandInterface $addToCartCommand */
                $addToCartCommand = $event->getData();

                /** @var OrderItemInterface $cartItem */
                $cartItem = $addToCartCommand->getCartItem();

                Assert::isInstanceOf($cartItem->getVariant(), ProductVariantInterface::class);

                $cartItem->setVariant($cartItem->getVariant()->getSample());

                /** @var ChannelInterface $channel */
                $channel = $this->channelContext->getChannel();

                Assert::isInstanceOf($channel, ChannelInterface::class);

                if (null === $channel->getMaxSamplesPerOrder() || 0 === $channel->getMaxSamplesPerOrder()) {
                    return;
                }

                $samples = $cartItem->getQuantity();

                /** @var OrderItemInterface $item */
                foreach ($addToCartCommand->getCart()->getItems() as $item) {
                    Assert::isInstanceOf($item->getVariant(), ProductVariantInterface::class);

                    if (null === $item->getVariant()->getSampleOf()) {
                        continue;
                    }

                    $samples += $item->getQuantity();
                }

                if ($samples > $channel->getMaxSamplesPerOrder()) {
                    $event->getForm()->addError(new FormError(
                        'babdev_sylius_product_samples.order.allowed_samples.max_per_order',
                        null,
                        ['{{ limit }}' => $channel->getMaxSamplesPerOrder()],
                        $channel->getMaxSamplesPerOrder(),
                    ));
                }
            }, -10);
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [AddToCartType::class];
    }
}
