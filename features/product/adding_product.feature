@managing_products_with_product_samples
Feature: Adding a new product
    In order to extend my merchandise
    As an Administrator
    I want to add a new product to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Standard" shipping category
        And I am logged in as an administrator

    @ui @api
    Scenario: Adding a new simple product with product samples
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its slug to "dice-brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I enable product samples
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Dice Brewing" should appear in the store
