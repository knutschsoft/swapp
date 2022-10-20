Feature: Testing team create resource with guests

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | roles      | client        |
      | admin@gmx.de | ROLE_ADMIN | client@gmx.de |

  @api @apiTeamCreate @guests
  Scenario: I can request /api/teams/create as an admin and create a team with guests
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/create" with parameters:
      | key                 | value                             |
      | client              | clientIri<client@gmx.de>          |
      | name                | Religion                          |
      | ageRanges           | ageRanges<1-3>                    |
      | users               | userIris<admin@gmx.de>            |
      | locationNames       | array<City, Spielplatz>           |
      | isWithAgeRanges     | <true>                            |
      | isWithContactsCount | <false>                           |
      | isWithUserGroups    | <false>                           |
      | isWithGuests        | <true>                            |
      | guestNames          | array<Karla, Opa Manfred, Alfons> |
      | userGroupNames      | array<>                           |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type        | Team                            |
      | name         | Religion                        |
      | isWithGuests | <true>                          |
      | guestNames   | array<Alfons,Karla,Opa Manfred> |
