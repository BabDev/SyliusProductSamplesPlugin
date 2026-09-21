@requesting_product_samples
Feature: Limiting how many samples an order may contain
    In order to keep sample requests from being abused
    As a Store Owner
    I want an order to carry no more samples than the channel allows

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Wyborowa Vodka" priced at "$100.00"
        And this product has product samples enabled for all channels
        And the store has a product "Zubrowka Vodka" priced at "$80.00"
        And this product has product samples enabled for all channels

    @ui @javascript
    Scenario: Requesting a sample up to the channel limit
        Given the store allows 2 samples per order
        And I view product "Wyborowa Vodka"
        And I request a sample
        When I view product "Zubrowka Vodka"
        And I request a sample
        Then there should be 2 items in my cart

    @ui @javascript
    Scenario: Requesting one sample too many
        Given the store allows 1 sample per order
        And I view product "Wyborowa Vodka"
        And I request a sample
        When I view product "Zubrowka Vodka"
        And I try to request a sample
        Then I should be notified that I cannot request more than 1 sample per order
        When I see the summary of my cart
        Then there should be one item in my cart

    @ui @javascript
    Scenario: The limit does not apply when the channel sets none
        Given the store does not limit how many samples an order may contain
        And I view product "Wyborowa Vodka"
        And I request a sample
        When I view product "Zubrowka Vodka"
        And I request a sample
        Then there should be 2 items in my cart

    @api
    Scenario: Requesting samples up to the channel limit through the API
        Given the store allows 2 samples per order
        When I add "Sample - Wyborowa Vodka" variant of product "Wyborowa Vodka" to the cart
        And I add "Sample - Zubrowka Vodka" variant of product "Zubrowka Vodka" to the cart
        Then there should be 2 items in my cart

    @api
    Scenario: Requesting one sample too many through the API
        Given the store allows 1 sample per order
        When I add "Sample - Wyborowa Vodka" variant of product "Wyborowa Vodka" to the cart
        And I add "Sample - Zubrowka Vodka" variant of product "Zubrowka Vodka" to the cart
        Then I should be told that I cannot request more than 1 sample per order
        And there should be 1 item in my cart

    @api
    Scenario: The limit does not apply through the API when the channel sets none
        Given the store does not limit how many samples an order may contain
        When I add "Sample - Wyborowa Vodka" variant of product "Wyborowa Vodka" to the cart
        And I add "Sample - Zubrowka Vodka" variant of product "Zubrowka Vodka" to the cart
        Then there should be 2 items in my cart
