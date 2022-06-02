Feature: An admin can change visited at of a wayPoint

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | roles      | client        |
      | karl@gmx.de  |            | client@gmx.de |
      | admin@gmx.de | ROLE_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users       | client        |
      | Westhang | karl@gmx.de | client@gmx.de |
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

  @javascript
  @wayPoint @visitedAt
  Scenario: I can create a wayPoint with visitedAt and change visitedAt of a wayPoint
    Given I am authenticated as "karl@gmx.de"

    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "Jugo geht ab" in "oneOnOneInterview" field
    Then I wait for 'date<now,d.m.Y>' to appear
    Then I wait for 'date<now,H:i>' to appear
    Then I wait for 'date<now,N>' to appear

    And I click on text "Wegpunkt speichern und Runde abschließen"
    And I wait for "Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden." to appear
    And there are exactly 1 wayPoints in database
    And I can find the following wayPoints in database:
      | locationName | visitedAt             |
      | Assieck      | date<now,d.m.Y H:i:s> |

    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt/wayPointId<Assieck>/detail"
    Then I wait for "Ankunft" to appear
    Then I wait for 'Die Ankunftszeit muss nach der Startzeit der Runde (' to disappear

    Then I go to swapp page "/abmeldung"
    Given I am authenticated as "admin@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt/wayPointId<Assieck>/detail"
    Then I wait for 'Die Ankunftszeit muss nach der Startzeit der Runde (' to appear

    Then the element "button-way-point-submit" should be enabled
    When I click on element "visitedAtTime"
    Then I wait for "Schließen" to appear
    When I click on aria label "Verringern"
    Then the element "button-way-point-submit" should be disabled
    When I click on aria label "Erhöhen"
    When I click on aria label "Erhöhen"
    Then the element "button-way-point-submit" should be enabled

    When I click on element "button-way-point-submit"
    And I wait for "Wegpunkt geändert" to appear
    And I wait for 'Der Wegpunkt "Assieck" wurde erfolgreich geändert.' to appear
    And I can find the following wayPoints in database:
      | locationName | visitedAt                 |
      | Assieck      | date<+1 hour,d.m.Y H:i:s> |
