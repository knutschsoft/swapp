Feature: An user can do a walk with preset walkTeamMembers

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email       | client        | isEnabled | roles |
            | karl@gmx.de | client@gmx.de | 1         |       |
        Given the following teams exists:
            | name     | users       | client        | guestNames                         | isWithGuests | isWithHolidays | isWithWeather |
            | Westhang | karl@gmx.de | client@gmx.de | array<Diakonie, Krankenpflege,AWO> | <true>       | <false>        | <false>       |

    @javascript
    @walkCreate @guestNames
    Scenario: I do a walk with one wayPoint and will have walkTeamMembers pre selected by my last walk
        Given I am authenticated as "karl@gmx.de"
        And I should be on "/dashboard"
        Then I wait for "Team 'Westhang'" to appear
        When I click on text "Runde beginnen"
        Then I wait for "Team 'Westhang'" to disappear

        Then I wait for "Name" to appear
        Then I wait for "Tageskonzept" to appear
        When I enter "Mein erster Lauf" in "Name" field
        When I enter "Freies Streetwork" in "Tageskonzept" field

        When I wait for "Diakonie" to disappear
        When I wait for "Krankenpflege" to disappear
        When I wait for "AWO" to disappear
        When I click on test element "walk-guest-names-field"

        When I wait for "Diakonie" to appear
        When I wait for "Krankenpflege" to appear
        When I wait for "AWO" to appear

        When I enter "Ronin" in "walk-guest-names-field" field

        When I submit Runde beginnen formular

        Then there are exactly 1 walks in database
        And I can find the following walks in database:
            | name             | guestNames   |
            | Mein erster Lauf | array<Ronin> |
