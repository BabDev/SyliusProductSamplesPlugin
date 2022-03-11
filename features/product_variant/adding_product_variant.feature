@managing_product_variants_with_product_samples
Feature: Adding a new product variant
    In order to sell different variations of a single product with samples
    As an Administrator
    I want to add a new product variant to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" with values "Orange" and "Melon"
        And the store has "Fragile" shipping category
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new product variant when the product does not have samples enabled
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"

    @ui
    Scenario: Adding a new product variant with free samples
        Given this product has product samples enabled for all channels
        And I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I set its sample price to "$0.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"

    @ui
    Scenario: Adding a new product variant with price for its sample
        Given this product has product samples enabled for all channels
        And I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I set its sample price to "$10.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should have its sample priced at $10.00 for channel "United States"

    @ui
    Scenario: Adding a new product variant with discounted price for its sample
        Given this product has product samples enabled for all channels
        And I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I set its sample price to "$2.50" for "United States" channel
        And I set its original sample price to "$10.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should have its sample priced at $2.50 for channel "United States"
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should have its sample originally priced at $10.00 for channel "United States"

    @ui
    Scenario: Adding a new product variant with specific shipping category for its sample
        Given this product has product samples enabled for all channels
        And I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I set its sample price to "$10.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should have its sample priced at $10.00 for channel "United States"
