Feature: An administrator can change startTime of a walk

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | roles      | client        |
      | admin@gmx.de | ROLE_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users        | ageRanges          | client        |
      | Westhang | admin@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name   | team     | startTime       | weather | isUnfinished |
      | Klippe | Westhang | 02.01.2021 7:20 | Sonne   | true         |

  @javascript
  @walkChangeUnfinished
  Scenario: I can change an unfinished walk as an admin
    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name   | startTime                         | weather |
      | Klippe | date<02.01.2021 7:20,d.m.Y H:i:s> | Sonne   |
    Given I am authenticated as "admin@gmx.de"
    When I go to swapp page "/runde/walkId<Klippe>/detail"
    Then I wait for 'Runde "Klippe" ändern' to appear

    When I enter "Arschkalt" in "Wetter" field

    When I click on element "button-walk-submit"

    Then I wait for 'Runde "Klippe" wurde erfolgreich geändert.' to appear


    Then I can find the following walks in database:
      | name   | startTime                         | weather   |
      | Klippe | date<02.01.2021 7:20,d.m.Y H:i:s> | Arschkalt |


    And there are exactly 1 walks in database
