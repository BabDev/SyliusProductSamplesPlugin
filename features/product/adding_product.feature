@managing_products_with_product_samples
Feature: Adding a new product
    In order to extend my merchandise
    As an Administrator
    I want to add a new product to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Standard" shipping category
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Adding a new simple product without product samples
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its slug to "dice-brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Dice Brewing" should appear in the store
        And product "Dice Brewing" should have product samples disabled

    @ui @no-api
    Scenario: Adding a new simple product with free product samples
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its slug to "dice-brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I enable product samples
        And I set its sample price to "$0.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Dice Brewing" should appear in the store
        And product "Dice Brewing" should have product samples enabled

    @ui @no-api
    Scenario: Adding a new simple product with product samples with price
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its slug to "dice-brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I enable product samples
        And I set its sample price to "$2.50" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Dice Brewing" should appear in the store
        And product "Dice Brewing" should have product samples enabled

    @ui @no-api
    Scenario: Adding a new simple product with product samples with discounted price
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its slug to "dice-brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I enable product samples
        And I set its sample price to "$1.00" for "United States" channel
        And I set its original sample price to "$2.50" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Dice Brewing" should appear in the store
        And product "Dice Brewing" should have product samples enabled

    @ui @no-api
    Scenario: Adding a new simple product with product samples with specific shipping category
        Given I want to create a new simple product
        When I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its slug to "dice-brewing" in "English (United States)"
        And I set its price to "$10.00" for "United States" channel
        And I enable product samples
        And I set its sample price to "$0.00" for "United States" channel
        And I set its sample shipping category as "Standard"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Dice Brewing" should appear in the store
        And product "Dice Brewing" should have product samples enabled

    @ui @no-api
    Scenario: Adding a new configurable product without product samples
        Given the store has a product option "Bottle size" with a code "bottle_size"
        And this product option has the "0.7" option value with code "bottle_size_medium"
        And this product option has also the "0.5" option value with code "bottle_size_small"
        And I want to create a new configurable product
        When I specify its code as "WHISKEY_GENTLEMEN"
        And I name it "Gentleman Jack" in "English (United States)"
        And I add the "Bottle size" option to it
        And I set its slug to "whiskey/gentleman-jack" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Gentleman Jack" should appear in the store
        And product "Gentleman Jack" should have product samples disabled

    @ui @no-api
    Scenario: Adding a new configurable product with product samples
        Given the store has a product option "Bottle size" with a code "bottle_size"
        And this product option has the "0.7" option value with code "bottle_size_medium"
        And this product option has also the "0.5" option value with code "bottle_size_small"
        And I want to create a new configurable product
        When I specify its code as "WHISKEY_GENTLEMEN"
        And I name it "Gentleman Jack" in "English (United States)"
        And I add the "Bottle size" option to it
        And I set its slug to "whiskey/gentleman-jack" in "English (United States)"
        And I enable product samples
        And I add it
        Then I should be notified that it has been successfully created
        And the product "Gentleman Jack" should appear in the store
        And product "Gentleman Jack" should have product samples enabled
