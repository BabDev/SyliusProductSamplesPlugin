@requesting_product_samples
Feature: Requesting a product sample
    In order to try a product before committing to it
    As a Customer
    I want to request a sample from the product page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Wyborowa Vodka" priced at "$100.00"

    @ui @javascript
    Scenario: A product without samples offers no sample
        When I check this product's details
        Then I should not be able to request a sample of this product

    @ui @javascript
    Scenario: Requesting a free sample of a product
        Given this product has product samples enabled for all channels
        When I check this product's details
        Then I should be able to request a sample of this product
        And I should be offered a free sample
        When I request a sample
        Then I should be on my cart summary page
        And there should be one item in my cart
        And this item should have code "SAMPLE-WYBOROWA_VODKA"

    @ui @javascript
    Scenario: Requesting a paid sample of a product
        Given this product has product samples enabled for all channels priced at "$5.00"
        When I check this product's details
        Then I should be offered a sample priced at "$5.00"
        When I request a sample
        Then there should be one item in my cart
        And my cart total should be "$5.00"

    @ui @javascript
    Scenario: The sample is added instead of the product itself
        Given this product has product samples enabled for all channels priced at "$5.00"
        When I check this product's details
        And I request a sample
        Then there should be one item in my cart
        And my cart total should be "$5.00"
