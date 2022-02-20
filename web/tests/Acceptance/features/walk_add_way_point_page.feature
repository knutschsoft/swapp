Feature: An user can go to wayPoint add route and see all already created wayPoints

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | lonely@gmx.de     |                  | client@gmx.de |
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users         | ageRanges          | client        |
      | Westhang | karl@gmx.de   | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de | 1-10,3-12, 13 - 90 | gamer@gmx.de  |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |
      | How old? | gamer@gmx.de  |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name    | team     |
      | Gorbitz | Westhang |
      | Center  | Gamers   |
    Given the following way points exists:
      | locationName | walkName | beobachtung             | einzelgespraech |
      | Elbamare     | Gorbitz  | Nichts großartig neues. | Jugo geht ab.   |
      | Block17      | Gorbitz  |                         |                 |
      | Gamestop     | Center   |                         |                 |

  @javascript
  @wayPoint
  Scenario: I can request a wayPoint route directly as an user without a group
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then I wait for "Elbamare" to appear
    Then I wait for "Block17" to appear
    Then I wait for "Gamestop" to disappear
