<?php

declare(strict_types=1);

namespace spec\BabDev\SyliusProductSamplesPlugin\Form\EventSubscriber;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariantInterface;
use BabDev\SyliusProductSamplesPlugin\Synchronizer\ProductVariantOptionValuesSynchronizerInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\FormEvent;

final class SynchronizeSampleProductVariantOptionValuesFormSubscriberSpec extends ObjectBehavior
{
    public function let(ProductVariantOptionValuesSynchronizerInterface $optionValuesSynchronizer): void
    {
        $this->beConstructedWith($optionValuesSynchronizer);
    }

    public function it_does_nothing_when_there_is_no_data(FormEvent $event): void
    {
        $event->getData()->willReturn(null);

        $this->onSubmit($event);
    }

    public function it_synchronizes_option_values_from_the_variant_to_its_sample(
        ProductVariantOptionValuesSynchronizerInterface $optionValuesSynchronizer,
        FormEvent $event,
        ProductVariantInterface $sampleVariant,
    ): void {
        $event->getData()->willReturn($sampleVariant);

        $optionValuesSynchronizer->synchronize($sampleVariant)->shouldBeCalled();

        $this->onSubmit($event);
    }
}
