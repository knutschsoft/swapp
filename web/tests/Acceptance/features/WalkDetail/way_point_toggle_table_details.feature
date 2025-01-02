Feature: An user can toggle table rows on walk detail to see all

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email        | roles      | client        |
            | karl@gmx.de  |            | client@gmx.de |
        Given the following teams exists:
            | name     | users       | ageRanges          | client        |
            | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de |
        Given the following systemic questions exists:
            | question | client        |
            | How old? | client@gmx.de |
        Given the following tags exists:
            | name   | color     | client        |
            | Gewalt | Chocolate | client@gmx.de |
            | Drogen | Blue      | client@gmx.de |
        Given the following walks exists:
            | name    | team     |
            | Gorbitz | Westhang |
        Given the following way points exists:
            | locationName | walkName | beobachtung             | einzelgespraech | tags   |
            | Elbamare     | Gorbitz  | Nichts großartig neues. | Jugo geht ab.   | Drogen |
            | Nordbad      | Gorbitz  | Schicki.                | Jugo geht ab.   | Gewalt |

    @javascript
    @wayPoint @tableCollapse
    Scenario: I can request delete a wayPoint as an admin but not as a normal user
        Given I am authenticated as "karl@gmx.de"
        And I go to swapp page "/runde/walkId<Gorbitz>/detail"
        Then I wait for "Streetwork-Runde:" to appear
        Then I wait for "Alle Details anzeigen" to appear
        Then I wait for "Alle Details verbergen" to disappear
        Then I wait for "Drogen" to disappear
        Then I wait for "Gewalt" to disappear
        Then I click on test element "toggle-waypoint-details"
        Then I wait for "Drogen" to appear
        Then I wait for "Gewalt" to appear
        Then I wait for "Alle Details anzeigen" to disappear
        Then I wait for "Alle Details verbergen" to appear
        Then I click on test element "toggle-waypoint-details"
        Then I wait for "Alle Details anzeigen" to appear
        Then I wait for "Alle Details verbergen" to disappear
        Then I wait for "Drogen" to disappear
        Then I wait for "Gewalt" to disappear

