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

    public function testTheDefaultTemplatePrefixesTheVariantCode(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['sample_variant_code_template' => 'SAMPLE-{code}'],
        );
    }

    /**
     * @dataProvider provideValidTemplates
     */
    public function testConfigurationIsValidWithACustomTemplate(string $template): void
    {
        $this->assertProcessedConfigurationEquals(
            [['sample_variant_code_template' => $template]],
            ['sample_variant_code_template' => $template],
        );
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function provideValidTemplates(): iterable
    {
        yield 'prefix' => ['SAMPLE-{code}'];
        yield 'suffix' => ['{code}-SAMPLE'];
        yield 'wrapped' => ['S-{code}-S'];
        yield 'placeholder only' => ['{code}'];
    }

    public function testConfigurationIsInvalidWithAnEmptyTemplate(): void
    {
        $this->assertConfigurationIsInvalid([['sample_variant_code_template' => '']], 'sample_variant_code_template');
    }

    public function testConfigurationIsInvalidWhenTheTemplateHasNoPlaceholder(): void
    {
        $this->assertConfigurationIsInvalid(
            [['sample_variant_code_template' => 'SAMPLE-']],
            'must contain the "{code}" placeholder',
        );
    }

    public function testConfigurationIsInvalidWithAnUnknownOption(): void
    {
        $this->assertConfigurationIsInvalid([['sample_variant_code_prefix' => 'SAMPLE-']]);
    }
}
