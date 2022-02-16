@managing_channels_with_product_samples
Feature: Editing channel
    In order to change channel details
    As an Administrator
    I want to be able to edit a channel to enable product sample limits

    Background:
        Given the store operates on a channel named "Web Channel"
        And I am logged in as an administrator

    @ui
    Scenario: Setting a product sample limit for order on the channel
        Given I want to modify a channel "Web Channel"
        When I set its max number of samples per order to 4
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel should allow 4 samples per order
