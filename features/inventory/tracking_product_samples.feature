@managing_inventory_with_product_samples
Feature: Inventory tracking of sample variants
    In order to know what my store does with samples it holds stock of
    As a Store Owner
    I want the relationship between samples and inventory tracking to be defined

    # The plugin does not integrate with inventory. Samples stay out of the admin inventory screen only
    # because nothing ever marks one as tracked, and the screen lists tracked variants exclusively.

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has "Orange" variant priced at "$90.00"
        And this product has product samples enabled for all channels

    @domain
    Scenario: A generated sample is untracked, and so stays out of the inventory
        Then the sample of the "Orange" variant should not be tracked by the inventory
        And the inventory should not list the sample of the "Orange" variant

    @domain
    Scenario: Tracking the variant a sample belongs to leaves the sample untracked
        When the "Wyborowa Vodka" product is tracked by the inventory
        Then the "Orange" variant should be tracked by the inventory
        But the sample of the "Orange" variant should not be tracked by the inventory
        And the inventory should not list the sample of the "Orange" variant

    @domain
    Scenario: Nothing but the tracking flag keeps a sample out of the inventory
        Given the sample of the "Orange" variant is tracked by the inventory
        Then the inventory should list the sample of the "Orange" variant
