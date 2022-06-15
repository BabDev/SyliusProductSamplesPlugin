@managing_channels_with_product_samples
Feature: Adding a new channel
    In order to sell through multiple websites or mobile applications
    As an Administrator
    I want to add a new channel supporting product samples to the registry

    Background:
        Given the store has currency "Euro"
        And the store has locale "English (United States)"
        And the store operates in "United States" and "Poland"
        And I am logged in as an administrator

    @ui @api
    Scenario: Adding a new channel supporting product samples
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile channel"
        And I choose "Euro" as the base currency
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I set its max number of samples per order to 4
        And I set its sample product code prefix to "MOBILE-SAMPLE-"
        And I add it
        Then I should be notified that it has been successfully created
        And the channel "Mobile channel" should appear in the registry
        And channel "Mobile channel" should allow 4 samples per order
        And channel "Mobile channel" should have a sample product code prefix of "MOBILE-SAMPLE-"
