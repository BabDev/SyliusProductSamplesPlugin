@managing_product_variants_with_product_samples
Feature: Generating product variants
    In order to sell different variations of a single product
    As an Administrator
    I want to generate all possible product variants

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" with values "Orange" and "Melon"
        And I am logged in as an administrator

    @ui
    Scenario: Generating a product variant for product without variants when the product does not have samples enabled
        Given I want to generate new variants for this product
        When I specify that the 1st variant is identified by "WYBOROWA_ORANGE" code and costs "$90" in "United States" channel
        And I specify that the 2nd variant is identified by "WYBOROWA_MELON" code and costs "$95" in "United States" channel
        And I generate it
        Then I should be notified that it has been successfully generated
        And I should see 2 variants in the list

    @ui
    Scenario: Generating a product variant for product without variants when the product has samples enabled
        Given this product has product samples enabled for all channels
        And I want to generate new variants for this product
        When I specify that the 1st variant is identified by "WYBOROWA_ORANGE" code and costs "$90" in "United States" channel
        And I specify that the 2nd variant is identified by "WYBOROWA_MELON" code and costs "$95" in "United States" channel
        And I generate it
        Then I should be notified that it has been successfully generated
        And I should see 2 variants in the list
        And the variant with code "WYBOROWA_ORANGE" should have its sample priced at $0.00 for channel "United States"
        And the variant with code "WYBOROWA_MELON" should have its sample priced at $0.00 for channel "United States"

    @skipped-ui
    Scenario: Generating the rest of product variants for product with at least one when the product has samples enabled
        Given this product is available in "Melon" taste priced at "$95.00"
        And this product has product samples enabled for all channels
        And I want to generate new variants for this product
        When I specify that the 2nd variant is identified by "WYBOROWA_ORANGE" code and costs "$90" in "United States" channel
        And I generate it
        Then I should be notified that it has been successfully generated
        And I should see 2 variants in the list
        And the variant with code "WYBOROWA_ORANGE" should have its sample priced at $0.00 for channel "United States"

    @skipped-ui
    Scenario: Generating the rest of product variants for product with at least one when the product has samples enabled
        Given this product is available in "Orange" taste priced at "$90.00"
        And this product has product samples enabled for all channels
        And I want to generate new variants for this product
        When I specify that the 2nd variant is identified by "WYBOROWA_MELON" code and costs "$95" in "United States" channel
        And I generate it
        Then I should be notified that it has been successfully generated
        And I should see 2 variants in the list
        And the variant with code "WYBOROWA_MELON" should have its sample priced at $0.00 for channel "United States"

    @skipped-ui @javascript
    Scenario: Generating only a part of product variants when the product has samples enabled
        Given this product has product samples enabled for all channels
        And I want to generate new variants for this product
        When I specify that the 1st variant is identified by "WYBOROWA_ORANGE" code and costs "$90" in "United States" channel
        And I remove 2nd variant from the list
        And I generate it
        Then I should be notified that it has been successfully generated
        And I should see 1 variants in the list
        And the variant with code "WYBOROWA_ORANGE" should have its sample priced at $0.00 for channel "United States"
