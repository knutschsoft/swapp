Feature: An administrator can change startTime of a walk

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email       | roles      | client        |
      | karl@gmx.de | ROLE_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users       | ageRanges          | client        |
      | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name   | team     | startTime       | endTime          | reflection | systemicAnswer | commitments | insights |
      | Klippe | Westhang | 02.01.2021 7:20 | 10.01.2021 09:00 | zorp       | zorp           | zorp        | zorp     |

  @javascript
  @walkChange @startTime
  Scenario: I can change startTime of a walk as an admin
    And there are exactly 1 walks in database
    Given I am authenticated as "karl@gmx.de"
    When I go to swapp page "/runde/walkId<Klippe>/detail"
    Then I wait for 'Runde "Klippe" ändern' to appear

    When I click on element "startTimeTime"
    Then I wait for 'Schließen' to appear
    When I click on aria label "Erhöhen"

    When I click on element "startTimeDate"
    Then I wait for 'Mit den Pfeiltasten durch den Kalender navigieren' to appear
    When I click on aria label "Sonntag, 10. Januar 2021"


    When I click on element "button-walk-submit"

    Then I wait for 'Runde "Klippe" wurde erfolgreich geändert.' to appear


    Then I can find the following walks in database:
      | name   | startTime                         | endTime                           |
      | Klippe | date<10.01.2021 8:20,d.m.Y H:i:s> | date<10.01.2021 9:00,d.m.Y H:i:s> |


    And there are exactly 1 walks in database
