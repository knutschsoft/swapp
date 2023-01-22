Feature: An user can delete a walk

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | roles      | client        |
      | karl@gmx.de  |            | client@gmx.de |
      | admin@gmx.de | ROLE_ADMIN | client@gmx.de |
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
      | locationName | walkName | beobachtung             | einzelgespraech |
      | Elbamare     | Gorbitz  | Nichts großartig neues. | Jugo geht ab.   |

  @javascript
  @walk @delete @suw
  Scenario: I can not delete a walk as a normal user
    Given I am authenticated as "karl@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/detail"
    Then I wait for 'Streetwork-Runde: "Gorbitz"' to appear
    Then I wait for "runde löschen und zur Runde zurückkehren" to disappear

  @javascript
  @walk @delete @suw
  Scenario: I can delete a walk as an admin
    Given I am authenticated as "admin@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/detail"
    Then I wait for 'Streetwork-Runde: "Gorbitz"' to appear
    Then I wait for "runde löschen und zur Runde zurückkehren" to disappear
    When I click on element "button-walk-remove"
    Then I wait for "Bist du dir absolut sicher?" to appear

    Then the element "button-walk-remove-modal" should be disabled
    When I enter "Gorbitz" in "walkName" field
    Then the element "button-walk-remove-modal" should be enabled

    When I click on element "button-walk-remove-modal"
    Then I wait for 'Abgeschlossene Streetwork-Runden' to appear
    Then I wait for 'Gorbitz' to disappear

    And I can not find the following wayPoints in database:
      | locationName |
      | Elbamare     |
    And I can not find the following walks in database:
      | name    |
      | Gorbitz |
