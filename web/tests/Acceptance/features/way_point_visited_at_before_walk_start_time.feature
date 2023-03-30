Feature: An user can change startTime of a wayPoint on form of first wayPoint

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
      | name    | team     | startTime                 |
      | Gorbitz | Westhang | date<+4 hour,d.m.Y H:i:s> |

  @javascript
  @wayPoint @visitedAt
  Scenario: I can create a wayPoint with visitedAt and change startTime of a wayPoint
    Given I am authenticated as "karl@gmx.de"

    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "Jugo geht ab" in "oneOnOneInterview" field
    Then I wait for 'date<now,d.m.Y>' to appear
    Then I wait for 'date<now,H:i>' to appear
    Then I wait for 'date<now,N>' to appear

    And I wait for 'Hinweis: Die gewählte Ankunftszeit ist 4 Stunden vor dem Rundenstart.' to appear
    And I wait for 'Hier kannst du die Rundenstartzeit auf die aktuell gewählte Ankunftszeit ändern.' to appear

    When I click on element "button-set-walk-start-time"

    Then I wait for "Rundenbeginn geändert" to appear
    Then I wait for "Der Rundenbeginn wurde erfolgreich von" to appear
    And I wait for 'Hinweis: Die gewählte Ankunftszeit ist 4 Stunden vor dem Rundenstart.' to disappear
    And I can find the following walks in database:
      | name    | startTime             |
      | Gorbitz | date<now,d.m.Y H:i:s> |

    And I click on text "Wegpunkt speichern und Runde abschließen"
    And I wait for "Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden." to appear
    And there are exactly 1 wayPoints in database
    And I can find the following wayPoints in database:
      | locationName | visitedAt             |
      | Assieck      | date<now,d.m.Y H:i:s> |
