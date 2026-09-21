# Sylius Product Samples Plugin

The [Sylius](https://sylius.com/) product samples plugin adds support for product samples to a Sylius application.

## What It Does

- Adds a Product Samples tab to the admin product and product variant forms, where samples are switched on per product and priced per channel.
- Automatically generates a sample variant for every variant of a product with samples active.
- Adds a "Request a Sample" button to the product page.
- Ensures samples line items are correctly labeled throughout the application.
- Enforces an optional per-channel limit on how many samples one order may contain.

## Support Matrix

The below table shows the supported PHP, Symfony, and Sylius versions for this plugin.

| Version | Status             | PHP Versions | Symfony Versions | Sylius Versions |
|---------|--------------------|--------------|------------------|-----------------|
| 1.x     | Actively Supported | 8.1+         | 5.4, 6.4         | 1.12            |
