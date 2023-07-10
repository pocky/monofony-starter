@ui @backend @%entity_name%s
Feature: Deleting a %entity_name%
    In order to get rid of deprecated %entity_name%s
    As an Administrator
    I want to be able to delete a %entity_name%

    Background:
        Given I am logged in as an administrator
        And I already have 2 %entity_name%s
        And I already have "%entity_name%" %entity_name%

    Scenario: Deleting a %entity_name%
        Given I want to delete this %entity_name%
        Then I should be notified that it has been successfully deleted
        And I should not see the "%entity_name%" %entity_name% in the list
