Feature: A user can do a walk

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email         | client        |
      | karl@gmx.de   | client@gmx.de |
      | lonely@gmx.de | client@gmx.de |
    Given the following teams exists:
      | name     | users       | ageRanges          | client        | locationNames   | isWithSystemicQuestion |
      | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | City,Spielplatz | <true>                 |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |

  @javascript
  @walkCreate
  Scenario: I can not start a walk as an user without a group
    Given I am authenticated as "lonely@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Neue Streetwork-Runde" to appear
    Then I wait for "Um eine neue Runde zu erstellen, musst Du zuerst ein neues Team anlegen." to appear
    Then I wait for "Runde beginnen" to disappear

  @javascript
  @walkCreate
  Scenario: I select a team to start a walk but there will not be a walk created yet
    Given there are exactly 0 walks in database
    Given I am authenticated as "karl@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Team 'Westhang'" to appear
    When I click on text "Runde beginnen"
    Then I wait for "Team 'Westhang'" to disappear
    Then I wait for "Name" to appear
    Then I wait for "Tageskonzept" to appear
    Then I wait for "Rundenstartzeit" to appear
    And there are exactly 0 walks in database

  @javascript
  @walkCreate
  Scenario: I do a walk with one wayPoint
    Given I am authenticated as "karl@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Team 'Westhang'" to appear
    When I click on text "Runde beginnen"
    Then I wait for "Team 'Westhang'" to disappear

    Then I wait for "Name" to appear
    Then I wait for "Tageskonzept" to appear
    Then I wait for "Rundenstartzeit" to appear
    Then I wait for "Ferien" to appear
    Then I wait for "Wetter" to appear
    When I enter "Mein erster Lauf" in "Name" field
    When I enter "Mein erster Lauf" in "Tageskonzept" field
    When I enter "Arschkalt" in "Wetter" field
    When I click on text "Runde beginnen"
    Then I wait for "Runde beginnen" to disappear


    Then I wait for "Wegpunkte der Runde" to appear
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for 'Wegpunkt zur Runde "Mein erster Lauf" hinzufügen' to appear
    Then I wait for "Ort" to appear
    Then I wait for "Anzahl Personen vor Ort" to appear
    Then I wait for "Ergibt sich automatisch aus der Summe der Altersgruppen." to appear
    Then I wait for "Altersgruppen" to appear
    Then I wait for "1 - 10 m" to appear
    Then I wait for "3 - 12 w" to appear
    Then I wait for "13 - 90 x" to appear
    Then I wait for "Tags" to appear
    Then I wait for "Gewalt" to appear
    Then I wait for "Drogen" to appear
    Then the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "Jugo geht ab" in "oneOnOneInterview" field
    And I set browser window size to "1000" x "1800"
    And I click on text "Wegpunkt speichern und Runde abschließen"
    And there are exactly 1 walks in database

    Then I wait for 'Runde "Mein erster Lauf" abschließen' to appear
    Then I wait for 'Wegpunkt erfolgreich hinzugefügt. Die Runde kann jetzt abgeschlossen werden.' to appear

    When I enter "38" in "systemicAnswer" field
    When I enter "Nice day!" in "walkReflection" field
    When I enter "nächste Woche nochmal" in "commitments" field
    When I enter "Blockieren ist doof!" in "insights" field
    And I click on element with selector "[data-test='rating'] .vue-rate-it-rating-item:nth-child(4n)"
    And I click on text "Runde abschließen"

    And I wait for "Runde abschließen" to disappear
    #    And I wait for 'Runde "Mein erster Lauf" wurde erfolgreich erstellt.' to appear

    Then I should be on "/dashboard"
    And I wait for "Mein erster Lauf" to appear
    And I wait for "Runde ansehen" to appear
    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name             | rating | systemicAnswer | walkReflection | commitments           | insights             |
      | Mein erster Lauf | int<4> | 38             | Nice day!      | nächste Woche nochmal | Blockieren ist doof! |
