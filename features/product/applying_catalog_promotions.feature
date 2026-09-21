@applying_catalog_promotions_to_products_with_samples
Feature: Applying catalog promotions to a product with samples
    In order to price a promotion without being surprised by what it does to my samples
    As a Store Owner
    I want the effect of a catalog promotion on a sample to be knowable and bounded

    # Sylius scopes a catalog promotion by product or taxon, and a sample is a variant of the product it
    # belongs to, so a promotion scoped that way reaches the sample as well. The plugin takes no position
    # on whether it should — that is a merchandising decision. These scenarios pin what actually happens,
    # and that a discount can never drive a sample below zero.

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has "Orange" variant priced at "$100.00"
        And this product has product samples enabled for all channels priced at "$10.00"

    @domain
    Scenario: A promotion scoped at the product reaches the samples underneath it
        Given there is a catalog promotion "Autumn sale" that reduces price by fixed "$4.00" in the "United States" channel and applies on "Wyborowa Vodka" product
        Then the sample of the "Orange" variant should be priced at "$6.00", reduced from "$10.00"

    @domain
    Scenario: A discount larger than the sample price floors at zero rather than going negative
        Given there is a catalog promotion "Clearance" that reduces price by fixed "$50.00" in the "United States" channel and applies on "Wyborowa Vodka" product
        Then the sample of the "Orange" variant should be priced at "$0.00", reduced from "$10.00"

    @domain
    Scenario: The storefront offers the discounted sample price rather than the original
        Given there is a catalog promotion "Autumn sale" that reduces price by fixed "$4.00" in the "United States" channel and applies on "Wyborowa Vodka" product
        Then the storefront pricing for the "Orange" variant should offer its sample at "$6.00", reduced from "$10.00"

    @domain
    Scenario: A promotion scoped at another product leaves the samples alone
        Given the store has a "Zubrowka Vodka" configurable product
        And this product has "Lemon" variant priced at "$80.00"
        And there is a catalog promotion "Autumn sale" that reduces price by fixed "$4.00" in the "United States" channel and applies on "Zubrowka Vodka" product
        Then the sample of the "Orange" variant should not be discounted
