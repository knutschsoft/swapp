Feature: An user can do a walk without holidays

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email         | client        |
            | karl@gmx.de   | client@gmx.de |
            | lonely@gmx.de | client@gmx.de |
        Given the following teams exists:
            | name     | users       | ageRanges          | client        | locationNames   | isWithHolidays |
            | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | City,Spielplatz | <false>        |
        Given the following systemic questions exists:
            | question | client        |
            | How old? | client@gmx.de |
        Given the following tags exists:
            | name   | color     | client        |
            | Gewalt | Chocolate | client@gmx.de |
            | Drogen | Blue      | client@gmx.de |

    @javascript
    @walkCreate
    Scenario: I start a walk without holidays
        Given I am authenticated as "karl@gmx.de"
        And I should be on "/dashboard"
        Then I wait for "Team 'Westhang'" to appear
        When I click on text "Runde beginnen"
        Then I wait for "Team 'Westhang'" to disappear

        Then I wait for "Name" to appear
        Then I wait for "Tageskonzept" to appear
        Then I wait for "Rundenstartzeit" to appear
        Then I wait for "Ferien" to disappear
        Then I wait for "Wetter" to appear
        When I enter "Mein erster Lauf" in "Name" field
        When I enter "Arschkalt" in "Wetter" field
        When I enter "Mein erster Lauf" in "Tageskonzept" field
        When I submit Runde beginnen formular

        Then I wait for "Wegpunkte der Runde" to appear

        And there are exactly 1 walks in database
        And I can find the following walks in database:
            | name             | holidays | isWithHolidays |
            | Mein erster Lauf | string<> | <false>        |
