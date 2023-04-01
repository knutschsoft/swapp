Feature: An administrator can change a walk

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
      | name        | team     |
      | Spaziergang | Westhang |
    Given the following way points exists:
      | locationName | walkName    | tags           |
      | Assieck      | Spaziergang | Gewalt         |
      | Assieck2     | Spaziergang | Drogen         |
      | Assieck3     | Spaziergang | Drogen, Gewalt |

  @javascript @wayPointList @dashboard
  Scenario: I can see total items of filtered walk table and also use pagination with default filter of "Beginn"
    And there are exactly 3 wayPoints in database
    Given I am authenticated as "karl@gmx.de"
    Then I wait for 'Liste aller Wegpunkte (3)' to appear

    Then I wait for "Gewalt" to appear
    Then I wait for "Drogen" to appear
    Then I wait for "Polizei" to disappear
    Then I wait for "Schwangerschaft" to disappear

    When I go to swapp page "/runde/walkId<Spaziergang>/wegpunkt-hinzufuegen"
    Then I wait for "Gewalt" to appear
    Then I wait for "Polizei" to appear
    Then I wait for "Drogen" to disappear
    Then I wait for "Schwangerschaft" to disappear

    When I go to swapp page "/runde/walkId<Spaziergang>/wegpunkt/wayPointId<Assieck>/detail"
    Then I wait for "Gewalt" to appear
    Then I wait for "Polizei" to appear
    Then I wait for "Drogen" to disappear
    Then I wait for "Schwangerschaft" to disappear

    When I go to swapp page "/runde/walkId<Spaziergang>/wegpunkt/wayPointId<Assieck2>/detail"
    Then I wait for "Gewalt" to appear
    Then I wait for "Polizei" to appear
    Then I wait for "Drogen" to appear
    Then I wait for "Schwangerschaft" to disappear
