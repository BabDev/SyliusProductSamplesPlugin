<?php

declare(strict_types=1);

namespace Tests\BabDev\SyliusProductSamplesPlugin\Functional\DependencyInjection;

use BabDev\SyliusProductSamplesPlugin\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    public function testConfigurationIsValidWithNoUserConfiguration(): void
    {
        $this->assertConfigurationIsValid([[]]);
    }

    public function testConfigurationIsValidWithCustomPrefix(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['sample_variant_code_prefix' => 'custom-prefix-']],
            ['sample_variant_code_prefix' => 'custom-prefix-'],
        );
    }

    public function testConfigurationIsInvalidWithEmptyPrefix(): void
    {
        $this->assertConfigurationIsInvalid([['sample_variant_code_prefix' => '']], 'sample_variant_code_prefix');
    }
}
