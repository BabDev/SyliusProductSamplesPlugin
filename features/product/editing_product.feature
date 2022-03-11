@managing_products_with_product_samples
Feature: Editing a product
    In order to change product details
    As an Administrator
    I want to be able to edit a product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Dice Brewing"
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Disabling product samples
        Given the "Dice Brewing" product has product samples enabled for all channels
        And I want to modify the "Dice Brewing" product
        When I disable product samples for it
        And I save my changes
        Then this product should have product samples disabled

    @ui @no-api
    Scenario: Changing a simple product sample's price
        Given the "Dice Brewing" product has product samples enabled for all channels
        And I want to modify the "Dice Brewing" product
        When I change its sample price to $15.00 for "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should have its sample priced at $15.00 for channel "United States"

    @ui @no-api
    Scenario: Changing a simple product sample's discount price
        Given the "Dice Brewing" product has product samples enabled for all channels
        And I want to modify the "Dice Brewing" product
        When I change its sample price to $10.00 for "United States" channel
        And I change its original sample price to "$15.00" for "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should have its sample priced at $10.00 for channel "United States"
        And it should have its sample originally priced at $15.00 for channel "United States"
