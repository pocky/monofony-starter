@api @%entity_name%s
Feature: Editing a %entity_name% in api
    As an administrator
    I want to edit %entity_name% in api
    So that I can edit %entity_name% in api

    Background:
        Given I am logged in as an administrator
        And I already have "%entity_name%" %entity_name%

    Scenario: Renaming a %entity_name%
        Given I want to update this %entity_name%
        When I change its %entity_identifier% to "%entity_name% edited"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And I should see the %entity_name% "%entity_name% edited" in the list
