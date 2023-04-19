Feature: Testing team change resource with guests

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

  @api @apiTeamChange @guests
  Scenario: I can request /api/teams/change as an admin and change a team with guests
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                 | value                     |
      | team                | teamIri<Westhang>         |
      | name                | Religion                  |
      | ageRanges           | ageRanges<1-3>            |
      | users               | userIris<admin@gmx.de>    |
      | locationNames       | array<City, Spielplatz>   |
      | walkNames           | array<>                   |
      | isWithAgeRanges     | <true>                    |
      | isWithPeopleCount   | <true>                    |
      | isWithContactsCount | <false>                   |
      | isWithUserGroups    | <false>                   |
      | isWithGuests        | <true>                    |
      | guestNames          | array<Opa Manfred, Karla> |
      | userGroupNames      | array<>                   |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type        | Team                     |
      | name         | Religion                 |
      | isWithGuests | <true>                   |
      | guestNames   | array<Karla,Opa Manfred> |
