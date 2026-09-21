@viewing_product_variants_with_product_samples
Feature: Viewing product variants through the shop API
    In order to not present samples as products a shopper can buy on their own
    As a Visitor
    I want sample variants left out of the shop API's variant listings

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has "Orange" variant priced at "$90.00"
        And this product has product samples enabled for all channels

    @api
    Scenario: Samples are left out of the product variant collection
        When I browse the available product variants
        Then the "Orange" variant should be among them
        But the sample of the "Orange" variant should not be among them

    @api
    Scenario: Samples are left out of the variants listed on a product
        When I ask for the "Wyborowa Vodka" product
        Then its variants should not include the sample of the "Orange" variant

    @api
    Scenario: A sample is still readable by its own IRI
        When I ask for the sample of the "Orange" variant directly
        Then I should be given its details
