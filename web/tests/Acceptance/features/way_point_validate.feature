Feature: An user can submit invalid entries in way point and get errors shown

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email         | roles | client        |
      | lonely@gmx.de |       | client@gmx.de |
    Given the following teams exists:
      | name     | ageRanges | client        |
      | Westhang | 1-10      | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name    | team     |
      | Gorbitz | Westhang |

  @javascript
  @wayPointValidate
  Scenario: I can request a non existing wayPoint as an user without a group and I will get redirected to dashboard
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then I wait for 'Wegpunkt zur Runde "Gorbitz" hinzufügen' to appear
    And the element "locationName" should be enabled
    When I enter "A" in "locationName" field
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    And I click on element "button-way-point-submit"
    And I wait for "Wegpunkt erfolgreich hinzugefügt." to disappear
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    Then I wait for "Diese Zeichenkette ist zu kurz. Sie sollte mindestens 2 Zeichen haben." to appear
