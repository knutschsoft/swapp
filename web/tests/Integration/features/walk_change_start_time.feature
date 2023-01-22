Feature: Testing walk change of time resource

  Background:
    Given the following clients exists:
      | email        |
      | gamer@gmx.de |
    Given the following users exists:
      | email          | roles      | client       |
      | karl@gamer.de  |            | gamer@gmx.de |
      | admin@gamer.de | ROLE_ADMIN | gamer@gmx.de |
    Given the following teams exists:
      | name   | users         | ageRanges | client       |
      | Gamers | karl@gamer.de |           | gamer@gmx.de |
    Given the following systemic questions exists:
      | question     | client       |
      | How are you? | gamer@gmx.de |
    Given the following walks exists:
      | name     | team   |
      | Gamescon | Gamers |
    Given the following way points exists:
      | locationName | walkName |
      | BOTW         | Gamescon |
      | BOTW2        | Gamescon |

  @api @walkChange
  Scenario: I can request /api/walks/change as authenticated user and will change a walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/change" with parameters:
      | key             | value                     |
      | walk            | walkIri<Gamescon>         |
      | name            | This is my Walk           |
      | conceptOfDay    | High and out.             |
      | weather         | Sonne                     |
      | walkTeamMembers | userIris<karl@gamer.de>   |
      | isResubmission  | <false>                   |
      | holidays        | <false>                   |
      | commitments     | narf                      |
      | insights        | zorp                      |
      | systemicAnswer  | zorp                      |
      | walkReflection  | zorp                      |
      | rating          | int<2>                    |
      | startTime       | 2021-05-11T15:51:06+00:00 |
      | endTime         | 2030-05-11T15:51:08+00:00 |
      | guestNames      | array<>                   |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk                      |
      | name             | This is my Walk           |
      | systemicQuestion | How are you?              |
      | weather          | Sonne                     |
      | startTime        | 2021-05-11T15:51:06+00:00 |
      | endTime          | 2030-05-11T15:51:08+00:00 |

    And there are exactly 1 walks in database
