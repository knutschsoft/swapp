Feature: An user can do a walk with preset walkTeamMembers

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | client        |
      | karl@gmx.de  | client@gmx.de |
      | pinky@gmx.de | client@gmx.de |
      | brain@gmx.de | client@gmx.de |
    Given the following teams exists:
      | name     | users                                 | ageRanges          | client        |
      | Westhang | karl@gmx.de,pinky@gmx.de,brain@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |

  @javascript
  @walkCreate
  Scenario: I do a walk with one wayPoint and will have walkTeamMembers pre selected by my last walk
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
    When I enter "Freies Streetwork" in "Tageskonzept" field
    When I enter "Arschkalt" in "Wetter" field

    When I click on element "walkTeamMember-pinky@gmx.de"

    When I click on element "startTimeTime"
    Then I wait for 'Schließen' to appear
    When I click on aria label "Verringern"

    When I click on text "Runde beginnen"
    Then I wait for "Runde beginnen" to disappear
    And I wait for "Wegpunkte der Runde" to appear

    Then there are exactly 1 walks in database
    And I can find the following walks in database:
      | name             | walkTeamMembers          |
      | Mein erster Lauf | karl@gmx.de,brain@gmx.de |

    And I go to "/abmeldung"
    Given I am authenticated as "brain@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Team 'Westhang'" to appear
    When I click on text "Runde beginnen"
    Then I wait for "Team 'Westhang'" to disappear

    Then I wait for "Name" to appear
    When I enter "Mein zweiter Lauf" in "Name" field
    When I enter "Mein erster Lauf" in "Tageskonzept" field
    When I enter "Arschkalt" in "Wetter" field

    When I click on element "startTimeDate"
    Then I wait for 'Mit den Pfeiltasten durch den Kalender navigieren' to appear
    When I click on aria label "Nächster Monat"
    When I click on aria label "Nächster Monat"
    When I click on text "26"

    When I click on element "walkTeamMember-karl@gmx.de"

    When I click on text "Runde beginnen"
    Then I wait for "Runde beginnen" to disappear
    And I wait for "Wegpunkte der Runde" to appear

    And there are exactly 2 walks in database

    And I can find the following walks in database:
      | name              | walkTeamMembers |
      | Mein zweiter Lauf | brain@gmx.de    |

    And I go to "/abmeldung"
    Given I am authenticated as "pinky@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Team 'Westhang'" to appear
    When I click on text "Runde beginnen"
    Then I wait for "Team 'Westhang'" to disappear

    Then I wait for "Name" to appear
    When I enter "Mein dritter Lauf" in "Name" field
    When I enter "Mein dritter Lauf" in "Tageskonzept" field
    When I enter "Arschkalt" in "Wetter" field

    Then I wait for field 'button-walk-create' to be not disabled
    When I click on element "button-walk-create"
    Then I wait for "Runde beginnen" to disappear
    And I wait for "Wegpunkte der Runde" to appear

    Then there are exactly 3 walks in database
    And I can find the following walks in database:
      | name              | walkTeamMembers           |
      | Mein dritter Lauf | brain@gmx.de,pinky@gmx.de |
