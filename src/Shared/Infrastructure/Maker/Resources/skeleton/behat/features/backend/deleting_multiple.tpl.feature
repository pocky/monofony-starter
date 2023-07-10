@ui @backend @%entity_name%s
Feature: Deleting multiple %entity_name%s
    In order to get rid of deprecated %entity_name%s in an efficient way
    As an Administrator
    I want to be able to delete multiple %entity_name%s at once

    Background:
        Given I am logged in as an administrator
        And I already have "%entity_name% 1" %entity_name%
        And I already have "%entity_name% 2" %entity_name%
        And I already have "%entity_name% 3" %entity_name%

    @javascript
    Scenario: Deleting multiple administrators at once
        When I am browsing %entity_name%s
        And I check the "%entity_name% 2" %entity_name%
        And I check the "%entity_name% 3" %entity_name%
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see 1 %entity_name%s in the list
        And I should see the %entity_name% "%entity_name% 1" in the list
        And I should not see the "%entity_name% 2" %entity_name% in the list
        And I should not see the "%entity_name% 3" %entity_name% in the list
