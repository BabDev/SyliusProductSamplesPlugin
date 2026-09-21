# Installation & Setup

To install this plugin, run the following [Composer](https://getcomposer.org/) command:

```bash
composer require babdev/sylius-product-samples-plugin
```

The plugin adds fields to the channel, product, and product variant models, so installing it is not only a matter of registering the bundle. The steps below cover a complete working install.

## Register The Plugin

For an application using Symfony Flex the plugin should be automatically registered, but if not you will need to add it to your `config/bundles.php` file.

```php
<?php

return [
    // ...

    BabDev\SyliusProductSamplesPlugin\BabDevSyliusProductSamplesPlugin::class => ['all' => true],
];
```

## Import the Configuration

In your `config/packages/_sylius.yaml` file, import the plugin's application configuration to automatically configure its integrations with other bundles.

```yaml
imports:
    - { resource: "@BabDevSyliusProductSamplesPlugin/Resources/config/app/config.yaml" }
```

This wires the product samples fields into the admin channel form. The storefront integrations are opt-in and are covered further down.

## Extend the Models

The plugin needs three extra pieces of data:

| Model            | Addition                                                                    |
|------------------|-----------------------------------------------------------------------------|
| `Channel`        | `maxSamplesPerOrder`, the per-order sample limit for that channel           |
| `Product`        | `samplesActive`, whether the product offers samples                         |
| `ProductVariant` | `sample` / `sampleOf`, the one-to-one link between a variant and its sample |

The channel side is offered as an interface and a trait so your application is not forced into an inheritance chain it may already be using. The product and variant sides are concrete classes to extend.

Create the three classes in your application:

```php
<?php

declare(strict_types=1);

namespace App\Entity\Channel;

use BabDev\SyliusProductSamplesPlugin\Model\ChannelInterface as ProductSamplesChannelInterface;
use BabDev\SyliusProductSamplesPlugin\Model\ChannelTrait as ProductSamplesChannelTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

class Channel extends BaseChannel implements ProductSamplesChannelInterface
{
    use ProductSamplesChannelTrait;
}
```

```php
<?php

declare(strict_types=1);

namespace App\Entity\Product;

use BabDev\SyliusProductSamplesPlugin\Model\Product as BaseProduct;

class Product extends BaseProduct
{
}
```

```php
<?php

declare(strict_types=1);

namespace App\Entity\Product;

use BabDev\SyliusProductSamplesPlugin\Model\ProductVariant as BaseProductVariant;

class ProductVariant extends BaseProductVariant
{
}
```

If your application already extends Sylius' `Product` and `ProductVariant`, change the class each one extends to the plugin's; the plugin's classes extend the Sylius ones.

Then point the resource configuration at them in `config/packages/_sylius.yaml`:

```yaml
sylius_channel:
    resources:
        channel:
            classes:
                model: 'App\Entity\Channel\Channel'

sylius_product:
    resources:
        product:
            classes:
                model: 'App\Entity\Product\Product'
        product_variant:
            classes:
                model: 'App\Entity\Product\ProductVariant'
                repository: 'BabDev\SyliusProductSamplesPlugin\Doctrine\ORM\ProductVariantRepository'
```

<div class="docs-note docs-note--tip"><strong>The repository is not optional.</strong> Sample variants are rows in the <code>sylius_product_variant</code> database table, so without the plugin's repository they show up alongside real variants in the admin product variant grid. The plugin's repository extends Sylius' own and adds a single <code>sampleOf IS NULL</code> condition to the grid queries. If your application already replaces the product variant repository, extend the plugin's class instead of Sylius'.</div>

## Map the New Fields in Doctrine

The plugin ships no Doctrine mapping of its own, because the fields have to be mapped onto *your*
classes. Add the three mapping files below to your application's mapping directory, and register
that directory if it is not already:

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        mappings:
            App:
                type: xml
                dir: '%kernel.project_dir%/config/doctrine'
                prefix: App\Entity
```

`config/doctrine/Channel.orm.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <mapped-superclass name="App\Entity\Channel\Channel" table="sylius_channel">

        <field name="maxSamplesPerOrder" column="max_samples_per_order" type="integer" nullable="true" />

    </mapped-superclass>

</doctrine-mapping>
```

`config/doctrine/Product.orm.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <mapped-superclass name="App\Entity\Product\Product" table="sylius_product">

        <field name="samplesActive" column="samples_active" type="boolean" />

    </mapped-superclass>

</doctrine-mapping>
```

`config/doctrine/ProductVariant.orm.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                      http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <mapped-superclass name="App\Entity\Product\ProductVariant" table="sylius_product_variant">

        <one-to-one field="sample" target-entity="Sylius\Component\Product\Model\ProductVariantInterface" mapped-by="sampleOf">
            <cascade>
                <cascade-all />
            </cascade>
        </one-to-one>

        <one-to-one field="sampleOf" target-entity="Sylius\Component\Product\Model\ProductVariantInterface" inversed-by="sample">
            <cascade>
                <cascade-persist />
            </cascade>
            <join-column name="sample_of_id" on-delete="SET NULL" />
        </one-to-one>

    </mapped-superclass>

</doctrine-mapping>
```

Once the mappings are in place, update your schema:

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

## Wire Up the Storefront

Everything above is enough for the admin. The storefront needs the blocks and template overrides below to make samples available to customers.

### Template Events

Add to `config/packages/_sylius.yaml`:

```yaml
sylius_ui:
    events:
        sylius.shop.cart.widget.popup:
            blocks:
                content:
                    enabled: false
                sample_aware_content:
                    template: "@BabDevSyliusProductSamplesPlugin/Shop/Cart/Widget/_popup.html.twig"
                    priority: 10

        sylius.shop.checkout.sidebar:
            blocks:
                summary:
                    enabled: false
                sample_aware_summary:
                    template: "@BabDevSyliusProductSamplesPlugin/Shop/Checkout/_summary.html.twig"
                    priority: 20

        sylius.shop.layout.javascripts:
            blocks:
                request_a_sample_javascript:
                    template: 'JavaScript/plugin.html.twig'
                    priority: 0
```

The two `enabled: false` entries disable Sylius' own blocks; the plugin's replacements label sample line items as samples rather than repeating the product name.

### Template Overrides

Several core templates expose neither a Twig block nor a `sylius_template_event` at the point the plugin needs to change, so the plugin carries forks of them. Each fork opens with a header naming its upstream path, the Sylius version it was taken from, and its one intended difference — diff them against upstream when you upgrade Sylius.

Override each one from your application with a one-line include:

```twig
{# templates/bundles/SyliusShopBundle/Product/Show/_addToCart.html.twig #}
{% include '@BabDevSyliusProductSamplesPlugin/Shop/Product/Show/_addToCart.html.twig' %}
```

```twig
{# templates/bundles/SyliusShopBundle/Product/Show/_variants.html.twig #}
{% include '@BabDevSyliusProductSamplesPlugin/Shop/Product/Show/_variants.html.twig' %}
```

```twig
{# templates/bundles/SyliusShopBundle/Product/Show/_variantsPricing.html.twig #}
{% include '@BabDevSyliusProductSamplesPlugin/Shop/Product/Show/_variantsPricing.html.twig' with {'pricing': pricing, 'variants': variants} %}
```

```twig
{# templates/bundles/SyliusShopBundle/Product/_info.html.twig #}
{% include '@BabDevSyliusProductSamplesPlugin/Shop/Product/_info.html.twig' %}
```

```twig
{# templates/bundles/SyliusAdminBundle/Product/_info.html.twig #}
{% include '@BabDevSyliusProductSamplesPlugin/Admin/Product/_info.html.twig' %}
```

What each one contributes:

| Override                             | Effect                                                                        |
|--------------------------------------|-------------------------------------------------------------------------------|
| `Shop/Product/Show/_addToCart`       | Adds the "request a sample" button next to "add to cart"                      |
| `Shop/Product/Show/_variants`        | Emits the sample price data attributes the shop script reads                  |
| `Shop/Product/Show/_variantsPricing` | Formats the sample price in the variant pricing map for products with options |
| `Shop/Product/_info`                 | Adds a sample badge to line items in the cart and order summaries             |
| `Admin/Product/_info`                | Adds a sample badge to line items in the admin order view                     |

If you have already forked any of these templates for your own reasons, merge the plugin's customizations into your template rather than replacing it.

### Shop JavaScript

The plugin ships a JavaScript component that swaps the sample button's label and price as the shopper selects a variant. Publish the bundle assets:

```bash
bin/console assets:install public
```

Then add the template referenced by the `sylius.shop.layout.javascripts` block above:

```twig
{# templates/JavaScript/plugin.html.twig #}
{% if app.request.attributes.get('_route') == 'sylius_shop_product_show' %}
    {% include '@SyliusUi/_javascripts.html.twig' with {'path': 'bundles/babdevsyliusproductsamplesplugin/babdev-product-samples-shop.js'} %}
{% endif %}
```

The route guard keeps the script off every other page; it is only useful on the product page. If you build your storefront assets with Webpack Encore, you can instead import the plugin's source from `src/Resources/assets/shop/` into your own entry point.
