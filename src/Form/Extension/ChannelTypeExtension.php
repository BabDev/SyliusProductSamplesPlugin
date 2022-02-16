<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maxSamplesPerOrder', IntegerType::class, [
                'required' => false,
                'label' => 'babdev_sylius_product_samples.form.channel.max_samples_per_order',
                'constraints' => [
                    new GreaterThanOrEqual(value: 1, groups: ['sylius']),
                ]
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
