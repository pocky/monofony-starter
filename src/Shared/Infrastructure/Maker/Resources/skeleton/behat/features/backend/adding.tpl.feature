@ui @backend @%entity_name%s
Feature: Adding a new %entity_name%
    In order to create new %entity_name%
    As an Administrator
    I want to add a %entity_name% in the admin panel

    Background:
        Given I am logged in as an administrator

    Scenario: Adding a new %entity_name%
        Given I want to create a new %entity_name%
        When I set its %entity_identifier% to "%entity_name%"
%entity_fields%
        And I add it
        Then I should be notified that it has been successfully created
        And I should see the %entity_name% "%entity_name%" in the list
