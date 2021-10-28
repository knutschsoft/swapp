Feature: An admin can see users of his client

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | steve@gmx.de      |                  | client@gmx.de |
      | bob@gmx.de        |                  | client@gmx.de |
      | lonely@gmx.de     |                  | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users                                            | ageRanges          | client        |
      | Westhang | karl@gmx.de,steve@gmx.de,bob@gmx.de,admin@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
      | RPG    | Lime      | gamer@gmx.de  |
    Given the following walks exists:
      | name        | team     | startTime  |
      | Gassi       | Westhang | now        |
      | Gassi2      | Westhang | last month |
      | Spaziergang | Westhang | last month |
      | Gamescon    | Westhang | now        |

  @javascript
  @userList
  Scenario: I can not see tag list as a non admin user
    Given I am authenticated as "lonely@gmx.de"
    When I am on "benutzer"
    Then I should be on "/dashboard"

  @javascript
  @userList
  Scenario: I can not see tag list as an admin user
    Given I am authenticated as "admin@gmx.de"
    When I am on "benutzer"
    Then I should be on "/benutzer"

    And I wait for "Liste der Benutzer" to appear
    And I wait for "Benutzername" to appear
    And I wait for "Erstellt am" to appear

    And I wait for "Neuen Benutzer erstellen" to appear

    And I wait for "Liste der aktiven Benutzer" to disappear

  @javascript
  @userList
  Scenario: I can see tag list as an super admin user
    Given I am authenticated as "superadmin@gmx.de"
    When I am on "benutzer"
    Then I should be on "/benutzer"

    And I wait for "Liste der Benutzer" to appear
    And I wait for "Benutzername" to appear
    And I wait for "Klient" to appear
    And I wait for "Erstellt am" to appear
    And I wait for "Geändert am" to appear

    And I wait for "Neuen Benutzer erstellen" to appear
    And I wait for "Zeitraum" to appear
    And I wait for "Summe" to appear
    And I wait for "4" to appear

    And I wait for "Liste der aktiven Benutzer" to appear
