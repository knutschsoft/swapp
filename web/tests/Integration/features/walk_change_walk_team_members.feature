Feature: Testing walk change resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
      | main@gmx.de   |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | lonely@gmx.de     |                  | client@gmx.de |
      | two@pac.de        |                  | client@gmx.de |
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | pinky@gamer.de    |                  | gamer@gmx.de  |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | admin@gamer.de    | ROLE_ADMIN       | gamer@gmx.de  |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following teams exists:
      | name     | users                        | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de       | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de                   | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de,pinky@gamer.de |                    | gamer@gmx.de  |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
      | How are you?   | gamer@gmx.de  |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name        | team   |
      | Spaziergang | CA     |
      | Gamescon    | Gamers |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |
      | BOTW         | Gamescon    |
      | BOTW2        | Gamescon    |

  @api @walkChange
  Scenario: I can request /api/walks/change as authenticated user and will try to change walkTeamMembers of a walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/change" with parameters:
      | key             | value                     |
      | walk            | walkIri<Gamescon>         |
      | name            | This is my Walk           |
      | conceptOfDay    | High and out.             |
      | weather         | Sonne                     |
      | walkTeamMembers | userIris<karl@gmx.de>     |
      | isResubmission  | <false>                   |
      | holidays        | <false>                   |
      | commitments     | narf                      |
      | insights        | zorp                      |
      | systemicAnswer  | zorp                      |
      | walkReflection  | zorp                      |
      | rating          | int<2>                    |
      | startTime       | 2021-05-11T15:51:06+00:00 |
      | endTime         | 2030-05-11T15:51:08+00:00 |
#    And print last response
    Then the response status code should be 400
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |

    And there are exactly 2 walks in database

  @api @walkChange
  Scenario: I can request /api/walks/change as authenticated user and will change walkTeamMembers of a walk

    Given I can find the following walks in database:
      | name     | walkTeamMembers              |
      | Gamescon | karl@gamer.de,pinky@gamer.de |
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
      | @type | Walk            |
      | name  | This is my Walk |

    Given I can find the following walks in database:
      | name            | walkTeamMembers |
      | This is my Walk | karl@gamer.de   |
      | Spaziergang     | two@pac.de      |

    And there are exactly 2 walks in database
