Feature: An user can create a wayPoint with and without contactsCount

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email       | roles | client        |
      | karl@gmx.de |       | client@gmx.de |
    Given the following teams exists:
      | name     | users       | isWithContactsCount | client        |
      | Westhang | karl@gmx.de | <false>             | client@gmx.de |
      | CA       | karl@gmx.de | <true>              | client@gmx.de |
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
  @wayPoint @contactsCount
  Scenario: I can create a wayPoint with and without contactsCount
    Given I am authenticated as "karl@gmx.de"

    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "Jugo geht ab" in "oneOnOneInterview" field
    Then I wait for 'Anzahl direkter Kontakte' to disappear
    And I wait for field "button-way-point-submit-and-finish" to be not disabled
    And I click on text "Wegpunkt speichern und Runde abschließen"
    And I wait for "Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden." to appear
    And there are exactly 1 wayPoints in database
    And I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Assieck      | <null>        |

    And I go to swapp page "/runde/walkId<Neuse>/wegpunkt-hinzufuegen"
    Then the element "locationName" should be enabled
    When I enter "Ackis" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "Jugo geht ab" in "oneOnOneInterview" field
    Then I wait for 'Anzahl direkter Kontakte' to appear
    When I enter "20" in "contactsCount" field
    And I click on text "Wegpunkt speichern und Runde abschließen"
    And I wait for "Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden." to appear
    And there are exactly 2 wayPoints in database
    And I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Ackis        | int<20>       |
