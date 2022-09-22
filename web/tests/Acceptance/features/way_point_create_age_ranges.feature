Feature: An user can create a wayPoint with and without ageRanges

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email       | roles | client        |
      | karl@gmx.de |       | client@gmx.de |
    Given the following teams exists:
      | name     | users       | isWithAgeRanges | client        | ageRanges |
      | Westhang | karl@gmx.de | <false>         | client@gmx.de |           |
      | CA       | karl@gmx.de | <true>          | client@gmx.de | 1-10      |
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
      | Neuse   | CA       |

  @javascript
  @wayPoint @ageRanges
  Scenario: I can create a wayPoint with and without ageRanges
    Given I am authenticated as "karl@gmx.de"

    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "Jugo geht ab" in "oneOnOneInterview" field
    Then I wait for 'Altersgruppen' to disappear
    And I click on text "Wegpunkt speichern und Runde abschließen"
    And I wait for "Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden." to appear
    And there are exactly 1 wayPoints in database
    And I can find the following wayPoints in database:
      | locationName | ageGroups |
      | Assieck      |           |

    And I go to swapp page "/runde/walkId<Neuse>/wegpunkt-hinzufuegen"
    Then the element "locationName" should be enabled
    When I enter "Ackis" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "Jugo geht ab" in "oneOnOneInterview" field
    Then I wait for 'Altersgruppen' to appear
    When I enter "7" in "1 - 10 m" field
    When I enter "3" in "1 - 10 w" field
    When I enter "1" in "1 - 10 x" field
    And I click on text "Wegpunkt speichern und Runde abschließen"
    And I wait for "Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden." to appear
    And there are exactly 2 wayPoints in database
    And I can find the following wayPoints in database:
      | locationName | ageGroups                   |
      | Ackis        | 1-10,m,7;1-10,w,3;1-10,x,1; |
