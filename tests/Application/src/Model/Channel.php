<?php

declare(strict_types=1);

namespace BabDev\SyliusProductSamplesPlugin\Tests\App\Model;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

class Channel extends BaseChannel implements ChannelInterface
{
    use ChannelTrait;
}
