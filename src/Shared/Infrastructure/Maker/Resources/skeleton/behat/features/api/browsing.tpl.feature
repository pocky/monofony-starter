@api @%entity_name%s
Feature: Browsing %entity_name%s through the API
    As an Administrator
    I want to browse %entity_name%s

    Background:
        Given I am logged in as an administrator
        And I already have 2 %entity_name%s
        And I already have "%entity_name%" %entity_name%

    Scenario: Browsing %entity_name%s in the api
        Given I want to browse %entity_name%s
        Then I should see 3 %entity_name%s in the list
        And I should see the %entity_name% "%entity_name%" in the list
