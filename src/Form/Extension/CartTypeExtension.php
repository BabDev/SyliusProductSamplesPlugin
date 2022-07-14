<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use Sylius\Bundle\OrderBundle\Form\Type\CartType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class CartTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private ChannelContextInterface $channelContext,
        private TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // This listener must run after the validation listener
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            /** @var OrderInterface $order */
            $order = $event->getData();

            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            Assert::isInstanceOf($channel, ChannelInterface::class);

            if (null === $channel->getMaxSamplesPerOrder() || 0 === $channel->getMaxSamplesPerOrder()) {
                return;
            }

            $samples = 0;

            /** @var OrderItemInterface $item */
            foreach ($order->getItems() as $item) {
                Assert::isInstanceOf($item->getVariant(), ProductVariantInterface::class);

                if (null === $item->getVariant()->getSampleOf()) {
                    continue;
                }

                $samples += $item->getQuantity();
            }

            if ($samples > $channel->getMaxSamplesPerOrder()) {
                $event->getForm()->addError(new FormError(
                    $this->translator->trans('babdev_sylius_product_samples.order.allowed_samples.plural_max_per_order', ['%count%' => $channel->getMaxSamplesPerOrder()], 'validators'),
                    null,
                    ['{{ limit }}' => $channel->getMaxSamplesPerOrder()],
                    $channel->getMaxSamplesPerOrder(),
                ));
            }
        }, -10);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CartType::class];
    }
}
