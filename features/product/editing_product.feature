@managing_products_with_product_samples
Feature: Editing a product
    In order to change product details
    As an Administrator
    I want to be able to edit a product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui
    Scenario: Disabling product samples
        Given the "Dice Brewing" product has product samples enabled
        And I want to modify the "Dice Brewing" product
        When I disable product samples for it
        And I save my changes
        Then this product should have product samples disabled
