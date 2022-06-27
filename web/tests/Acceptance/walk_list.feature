Feature: An administrator can change a walk

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
      | name     | team     | startTime       | endTime          | isUnfinished | reflection | systemicAnswer | commitments | insights |
      | Klippe1  | Westhang | 01.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe2  | Westhang | 02.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe3  | Westhang | 03.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe4  | Westhang | 04.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe5  | Westhang | 05.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe6  | Westhang | 06.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe7  | Westhang | 07.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe8  | Westhang | 08.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe9  | Westhang | 09.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
      | Klippe10 | Westhang | 10.01.2021 7:20 | 10.01.2021 09:10 | <false>      | zorp       | zorp           | zorp        | zorp     |

  @javascript
  @walkList
  Scenario: I can see total items of filtered walk table and also use pagination with default filter of "Beginn"
    And there are exactly 10 walks in database
    Given I am authenticated as "karl@gmx.de"
    Then I wait for 'Abgeschlossene Streetwork-Runden (10)' to appear

    Then I wait for "Klippe6" to appear
    Then I wait for "Klippe1 " to disappear
    Then I wait for "Fr., 01.01.2021, 07:20" to disappear
    Then I wait for "So., 10.01.2021, 07:20" to appear
    Then I wait for "09:10" to appear

    When I click on aria label "Go to page 2"
    Then I wait for "Klippe6" to disappear
    Then I wait for "Klippe1 " to appear
    Then I wait for "09:10" to disappear
    Then I wait for "Fr., 01.01.2021, 07:20" to appear
    Then I wait for "So., 10.01.2021, 07:20" to disappear
