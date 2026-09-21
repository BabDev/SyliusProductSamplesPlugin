@managing_product_variants_with_product_samples
Feature: Viewing sample variants through the admin API
    In order to administer the samples my store generates for me
    As an Administrator
    I want the admin API to keep showing me the sample variants a shopper never sees

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has "Orange" variant priced at "$90.00"
        And this product has product samples enabled for all channels
        And I am logged in as an administrator

    @api
    Scenario: Samples stay in the product variant collection
        When I browse the product variants
        Then the sample of the "Orange" variant should be listed

    @api
    Scenario: Samples stay among the variants listed on a product
        When I request the "Wyborowa Vodka" product
        Then its variants should include the sample of the "Orange" variant
