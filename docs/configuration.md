# Configuration

The plugin's own configuration is a single key. Most of what shapes its behavior is either per-channel data set in the admin, or a service you can swap.

## Bundle Configuration

```yaml
# config/packages/babdev_sylius_product_samples.yaml
babdev_sylius_product_samples:
    sample_variant_code_template: 'SAMPLE-{code}'
```

### `sample_variant_code_template`

**Type:** `string` **Default:** `SAMPLE-{code}`

The template used to build the code for a generated sample variant. The `{code}` placeholder is replaced with the code of the variant the sample belongs to.

| Template        | Variant code | Generated sample code |
|-----------------|--------------|-----------------------|
| `SAMPLE-{code}` | `MUG-BLUE`   | `SAMPLE-MUG-BLUE`     |
| `{code}-SAMPLE` | `MUG-BLUE`   | `MUG-BLUE-SAMPLE`     |
| `S-{code}-S`    | `MUG-BLUE`   | `S-MUG-BLUE-S`        |
| `{code}`        | `MUG-BLUE`   | `MUG-BLUE`            |

The template must contain the `{code}` placeholder and the container will refuse to compile without it.

## Per-Channel Configuration

### Max Samples Per Order

Each channel carries an optional limit for a maximum number of samples per order. Leaving it empty means the channel has no limit.

The limit is enforced by a validation constraint mapped onto the `Sylius\Component\Core\Model\Order` and `Sylius\Bundle\OrderBundle\Controller\AddToCartCommand` classes in the `sylius` validation group.

See [Usage](/open-source/packages/product-samples-plugin/docs/1.x/usage) for how the count is taken.

## Extension Points

Every collaborator below is defined behind an interface and aliased to its default implementation, so replacing one is a matter of pointing the alias at your own service.

### Sample Variant Code Generation

```php
namespace BabDev\SyliusProductSamplesPlugin\Generator;

interface SampleVariantCodeGeneratorInterface
{
    public function generate(ProductVariantInterface $sampleVariant): string;
}
```

Default: `TemplateSampleVariantCodeGenerator`, driven by the `sample_variant_code_template` configuration.

Implement this yourself when the template substitution is not enough.

```yaml
services:
    App\Generator\MyCodeGenerator: ~

    BabDev\SyliusProductSamplesPlugin\Generator\SampleVariantCodeGeneratorInterface:
        alias: App\Generator\MyCodeGenerator
```

### Sample Variant Name Generation

```php
namespace BabDev\SyliusProductSamplesPlugin\Generator;

interface SampleVariantNameGeneratorInterface
{
    public function generate(ProductVariantInterface $sampleVariant, ?string $locale = null): string;
}
```

Default: `TranslatedPrefixSampleVariantNameGenerator`, which renders the `babdev_sylius_product_samples.product_variant.prefixed_sample_name` translation (`Sample - %name%` in English) for each of the variant's locales.

If you only want to change the wording, override that translation key rather than the service.

### Variant Synchronizers

When a sample is generated or updated, two synchronizers copy data from the parent variant onto it:

| Interface                                         | Copies                              |
|---------------------------------------------------|-------------------------------------|
| `ProductVariantOptionValuesSynchronizerInterface` | The variant's product option values |
| `ProductVariantTranslationsSynchronizerInterface` | The variant's translated names      |

Replace either one to change what a sample inherits from its parent.

### Variant Pricing

`SampleAwareProductVariantPricesProvider` decorates Sylius' `sylius.provider.product_variants_prices` service and appends the `sample-price`, `sample-original-price`, and `free-sample` keys to each entry of the variant pricing map that the product show page renders. Because it is a decorator, Sylius' own promotion and original-price handling is untouched, and your own decoration of that service still applies.
