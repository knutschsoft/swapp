Feature: An user can request a non existing wayPoint and get redirected

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
  @wayPoint @delete
  Scenario: I can request delete a wayPoint as an admin but not as a normal user
    Given I am authenticated as "karl@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt/wayPointId<Elbamare>/detail"
    Then I wait for "Wegpunkt: Elbamare" to appear
    Then I wait for "Wegpunkt löschen und zur Runde zurückkehren" to disappear

  @javascript
  @wayPoint @delete
  Scenario: I can request delete a wayPoint as an admin but not as a normal user
    Given I am authenticated as "admin@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt/wayPointId<Elbamare>/detail"
    Then I wait for "Wegpunkt bearbeiten" to appear
    Then I wait for "Wegpunkt löschen und zur Runde zurückkehren" to appear
    When I click on element "button-way-point-remove"
    Then I wait for "Bist du dir absolut sicher?" to appear

    Then the element "button-way-point-remove-modal" should be disabled
    When I enter "Elbamare" in "wayPointName" field
    Then the element "button-way-point-remove-modal" should be enabled

    When I click on element "button-way-point-remove-modal"
    Then I wait for 'Wegpunkte der Runde "Gorbitz"' to appear

    And I can not find the following wayPoints in database:
      | locationName |
      | Elbamare     |
