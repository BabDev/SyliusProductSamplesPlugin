@managing_product_variants_with_product_samples
Feature: Hiding sample variants from the admin's variant searches
    In order to not have samples offered as if they were independently manageable variants
    As an Administrator
    I want sample variants left out of the variant searches the admin autocompletes against

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has "Orange" variant priced at "$90.00"
        And this product has product samples enabled for all channels

    @domain
    Scenario: Samples are left out of a search across all variants
        When I search all product variants for "Orange"
        Then the "Orange" variant should be among the results
        But the sample of the "Orange" variant should not be among the results

    @domain
    Scenario: Samples are left out of a search within a single product
        When I search the variants of this product for "Orange"
        Then the "Orange" variant should be among the results
        But the sample of the "Orange" variant should not be among the results

    @domain
    Scenario: A sample is still resolvable by its code
        Then the sample of the "Orange" variant should still be found by its code
