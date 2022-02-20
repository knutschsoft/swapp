Feature: An user will get redirected on a non existing walk create page

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
    Given the following users exists:
      | email         | roles      | client        |
      | karl@gmx.de   | ROLE_ADMIN | client@gmx.de |
      | karl@gamer.de | ROLE_ADMIN | gamer@gmx.de  |
      | oma@gamer.de  | ROLE_ADMIN | gamer@gmx.de  |
    Given the following teams exists:
      | name     | users         | ageRanges          | client        |
      | Westhang | karl@gmx.de   | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de | 1-10,3-12, 13 - 90 | gamer@gmx.de  |
      | Omas     | oma@gamer.de  | 1-10,3-12, 13 - 90 | gamer@gmx.de  |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |
      | How old? | gamer@gmx.de  |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |

  @javascript
  @walkPrologueRedirect
  Scenario: I can not start a walk when I can not access the team
    Given I am authenticated as "karl@gamer.de"
    When I go to swapp page "/runde/teamId<Westhang>/beginnen"
    Then I wait for "Dieses Team existiert nicht. Du wurdest auf das Dashboard weitergeleitet." to appear
    And I should be on "/dashboard"

  @javascript
  @walkPrologueRedirect
  Scenario: I can not start a walk when I do not belong to the team
    Given I am authenticated as "oma@gamer.de"
    When I go to swapp page "/runde/teamId<Gamers>/beginnen"
    Then I wait for "Du kannst für dieses Team keine Runde erstellen, da du kein Mitglied des Teams bist. Du wurdest auf das Dashboard weitergeleitet." to appear
    And I should be on "/dashboard"
