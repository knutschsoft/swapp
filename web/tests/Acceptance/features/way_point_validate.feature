Feature: An user can request a non existing wayPoint and get redirected

  Background:
    Given the following users exists:
      | email         | roles |
      | lonely@gmx.de |       |
    Given the following teams exists:
      | name     | ageRanges |
      | Westhang | 1-10      |
    Given the following tags exists:
      | name   | color |
      | Gewalt | Green |
      | Drogen | Blue  |
    Given the following walks exists:
      | name    | team     |
      | Gorbitz | Westhang |

  @javascript
  @wayPointValidate
  Scenario: I can request a non existing wayPoint as an user without a group and I will get redirected to dashboard
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walk<Gorbitz>/wegpunkt-hinzufuegen"
    Then I wait for 'Wegpunkt zur Runde "Gorbitz" hinzufügen' to appear
    And the element "Ort" should be enabled
    When I enter "A" in "Ort" field
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    And I click on element "save-way-point"
    And I wait for "Wegpunkt erfolgreich hinzugefügt." to disappear
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    Then I wait for "Diese Zeichenkette ist zu kurz. Sie sollte mindestens 2 Zeichen haben." to appear
