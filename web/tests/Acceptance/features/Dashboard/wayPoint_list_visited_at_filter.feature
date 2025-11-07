Feature: An user can filter waypoint list for visited at

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email       | roles      | client        |
            | karl@gmx.de | ROLE_ADMIN | client@gmx.de |
        Given the following teams exists:
            | name     | users       | client        |
            | Westhang | karl@gmx.de | client@gmx.de |
        Given the following teams exists:
            | name     | users       | client        |
            | Westhang | karl@gmx.de | client@gmx.de |
        Given the following tags exists:
            | name            | color     | client        | isEnabled |
            | Gewalt          | Chocolate | client@gmx.de | <true>    |
            | Drogen          | Blue      | client@gmx.de | <false>   |
            | Polizei         | Brown     | client@gmx.de | <true>    |
            | Schwangerschaft | Blue      | client@gmx.de | <false>   |
        Given the following walks exists:
            | name        | team     | startTime | endTime  |
            | Spaziergang | Westhang | 1.1.2025  | 2.4.2026 |
        Given the following way points exists:
            | locationName | walkName    | tags           | visitedAt |
            | Assieck      | Spaziergang | Gewalt         | 1.2.2025  |
            | Assieck2     | Spaziergang | Drogen         | 2.2.2025  |
            | Assieck3     | Spaziergang | Drogen, Gewalt | 1.4.2026  |

    @javascript @wayPointList @dashboard
    Scenario: I can see total items of filtered way point table and also filter for "Ankunft"
        And there are exactly 3 wayPoints in database
        Given I am authenticated as "karl@gmx.de"
        Then I wait for 'Liste aller Wegpunkte (3)' to appear
        And I wait for "01.04.2026" to appear
        When I set browser window size to "2200" x "1200"

        When I select date range von "01.02.2025" bis "03.02.2025" in date range picker "visited-at-filter"
        Then I wait for 'Liste aller Wegpunkte (2)' to appear
        And I wait for "01.04.2026" to disappear
