Feature: Testing walk change resource with guests

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | roles      | client        |
      | admin@gmx.de | ROLE_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users        | ageRanges          | client        | isWithGuests |
      | Westhang | admin@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | <true>       |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name        | team     |
      | Spaziergang | Westhang |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |

  @api @walkChange @guests
  Scenario: I can request /api/walks/change as authenticated user and will change guestNames of a walk

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/change" with parameters:
      | key             | value                     |
      | walk            | walkIri<Spaziergang>      |
      | name            | This is my Walk           |
      | conceptOfDay    | array<High and out.>      |
      | weather         | Sonne                     |
      | walkTeamMembers | userIris<admin@gmx.de>    |
      | isResubmission  | <false>                   |
      | holidays        | <false>                   |
      | commitments     | narf                      |
      | insights        | zorp                      |
      | systemicAnswer  | zorp                      |
      | walkReflection  | zorp                      |
      | rating          | int<2>                    |
      | startTime       | 2021-05-11T15:51:06+00:00 |
      | endTime         | 2030-05-11T15:51:08+00:00 |
      | guestNames      | array<Opa Manfred,Karla>  |
      | walkCreator     | userIri<admin@gmx.de>     |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type        | Walk                     |
      | name         | This is my Walk          |
      | isWithGuests | <true>                   |
      | guestNames   | array<Karla,Opa Manfred> |

    Given I can find the following walks in database:
      | name            | walkTeamMembers |
      | This is my Walk | admin@gmx.de    |

    And there are exactly 1 walks in database
