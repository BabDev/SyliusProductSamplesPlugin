# Installation & Setup

To install this plugin, run the following [Composer](https://getcomposer.org/) command:

```bash
composer require babdev/sylius-product-samples-plugin
```

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
