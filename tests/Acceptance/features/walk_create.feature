Feature: A user can do a walk

  Background:
    Given the following users exists:
      | email         |
      | karl@gmx.de   |
      | lonely@gmx.de |
    Given the following teams exists:
      | name     | users       | ageRanges          |
      | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 |
    Given the following systemic questions exists:
      | question |
      | How old? |
    Given the following tags exists:
      | name   | color |
      | Gewalt | Green |
      | Drogen | Blue  |

  @javascript
  @walkIgnore @ignore
  Scenario: I can not start a walk as an user without a group
    Given I am authenticated as "lonely@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Neue Streetwork-Runde" to appear
    Then I wait for "Du bist aktuell keinem Team zugeordnet." to appear
    Then I wait for "Runde beginnen" to disappear

  @javascript
  @walkCreate
  Scenario: I do a walk with on wayPoint
    Given I am authenticated as "karl@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Team 'Westhang'" to appear
    When I click on text "Runde beginnen"

    Then I wait for "Name" to appear
    Then I wait for "Tageskonzept" to appear
    Then I wait for "Rundenstartzeit" to appear
    Then I wait for "Ferien" to appear
    Then I wait for "Wetter" to appear
    When I enter "Mein erster Lauf" in "Name" field
    When I enter "Mein erster Lauf" in "Tageskonzept" field
    When I enter "Arschkalt" in "Wetter" field
    When I click on text "Runde beginnen"


    Then I wait for "Wegpunkte der Runde" to appear
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for 'Wegpunkt zur Runde "Mein erster Lauf" hinzufügen' to appear
    Then I wait for "Ort" to appear
    Then I wait for "Altersgruppen" to appear
    Then I wait for "1 - 10 m" to appear
    Then I wait for "3 - 12 w" to appear
    Then I wait for "13 - 90 x" to appear
    Then I wait for "Tags" to appear
    Then I wait for "Gewalt" to appear
    Then I wait for "Drogen" to appear
    When I enter "Assieck" in "Ort" field
    When I enter "Straßenbahnen sind blockiert" in "Beobachtung" field
    And I set browser window size to "800" x "1800"
    And I click on text "Wegpunkt speichern und Runde abschließen"

    Then I wait for 'Runde "Mein erster Lauf" abschließen' to appear
#    Then I wait for 'Wegpunkt "Assieck" wurde erfolgreich zur Runde "Mein erster Lauf" hinzugefügt.' to appear

    When I enter "38" in "Systemische Antwort" field
    When I enter "Nice day!" in "Reflexion" field
    When I enter "nächste Woche nochmal" in "commitments" field
    When I enter "Blockieren ist doof!" in "insights" field
    And I click on text "Runde abschließen"

    And I wait for "Runde abschließen" to disappear
    #    And I wait for 'Runde "Mein erster Lauf" wurde erfolgreich erstellt.' to appear

    Then I should be on "/dashboard"
    And I wait for "Mein erster Lauf" to appear
    And I wait for "Runde ansehen" to appear
