# Usage

## The Data Model

A sample is a product variant. It belongs to the same product as the variant it samples, and the two are joined one-to-one:

- `ProductVariant::getSample()` — the sample for this variant, or `null`
- `ProductVariant::getSampleOf()` — the variant this one is a sample *of*, or `null` for a normal variant

`sampleOf` is the discriminator. Anything in the plugin that has to tell a sample from a real variant tests it, and `null === $variant->getSampleOf()` is what "this is a real variant" means throughout.

Because a sample is an ordinary variant row, it carries its own channel pricing, shipping category, dimensions, weight, and tax category. Nothing special is needed to price, ship, or tax one.

## Enabling Samples on a Product

Samples are switched on per product, on the **Product Samples** tab of the admin product form. The tab holds a single *Samples Available* checkbox for a product with variants; for a simple product it also holds the sample's pricing, shipping, and tax fields.

Saving the product with samples active is what generates the samples. Event listeners for the `sylius.product.pre_create` and `sylius.product.pre_update` events walk the product's variants and creates a sample for every one that does not already have one, so:

- Enabling samples on a product that already has variants generates their samples on that save.
- Adding a variant later generates its sample when the product, or the variant itself, is next saved.
- A variant that already has a sample is left alone — samples are never regenerated or duplicated.

Switching *Samples Available* back off stops samples being offered, but does not delete the sample variants that already exist.

### What a Generated Sample Starts With

| Field                 | Initial value                                                                                 |
|-----------------------|-----------------------------------------------------------------------------------------------|
| Code                  | Built by the code generator                                                                   |
| Name (per locale)     | Built by the `babdev_sylius_product_samples.product_variant.prefixed_sample_name` translation |
| Option values         | Copied from the parent variant                                                                |
| Channel pricing       | Price **0** in every channel the product is enabled in                                        |
| Shipping / tax fields | Empty                                                                                         |

<div class="docs-note docs-note--tip"><strong>A generated sample is free until somebody prices it.</strong> The common use case is free samples, and that is how the plugin's pricing defaults.</div>

## Pricing and Editing a Sample

Sample product variants share a similar configuration as the core product variants they build on, with their settings in a dedicated "Product Samples" tab in the admin product management.

Setting an *original price* above the sample's price marks the sample as discounted, exactly as it does for a normal variant. The storefront reports it as a `data-sample-original-price` attribute alongside the price; the plugin's own JavaScript component does not render a struck-through price on the button, so using it is a theme decision.

## What the Shopper Sees

On the product page, a product with samples active renders a "Request a Sample" button beside the "Add to cart" button. The button's label reflects the selected variant's sample price in the current channel and is updated on-the-fly based on the customer's selection.

Clicking it submits the same add-to-cart form, and the plugin swaps the cart item's variant for that variant's sample before validation runs. The shopper gets the sample in their cart, not the product.

Sample line items are marked as samples wherever they appear, in one of two ways:

| Surface                             | Treatment                                         |
|-------------------------------------|---------------------------------------------------|
| Cart widget popup, checkout summary | The product name is prefixed with `Sample -`      |
| Cart summary and order line items   | A **Sample** tag is added beside the product name |
| Admin order view                    | A **Sample** tag is added beside the product name |

Sample variants are kept out of the places a shopper picks a product: they are excluded from the product's enabled variants, from the variant choices on the product page and in the cart, and from the admin product variant grid.

## Limiting Samples per Order

Each channel carries an optional "Max Samples Per Order" value on the admin channel form. Leaving it empty means that channel imposes no limit.

The field only accepts values of 1 or more, so an empty value is the only way to express "no limit".

When it is set, adding a sample that would take the order past the limit is rejected with a validation error, and so is editing cart quantities to the same effect. The limit counts the total **quantity** of sample items in the order, not the number of sample line items — three of one sample counts as three.
