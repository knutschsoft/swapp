Feature: Testing walk create resource with guest names

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email       | roles | client        |
      | karl@gmx.de |       | client@gmx.de |
    Given the following teams exists:
      | name     | users       | ageRanges          | client        | isWithGuests | guestNames     |
      | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | <false>      | array<>        |
      | CA       | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | <true>       | array<Liv,Tom> |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |

  @api @walkCreate @guests
  Scenario: I can request /api/walks/create with a team who has isWithGuests disabled
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                   |
      | team            | teamIri<Westhang>       |
      | name            | This is my Walk         |
      | conceptOfDay    | High and out.           |
      | weather         | Arschkalt               |
      | startTime       | 01.01.2020              |
      | walkTeamMembers | userIris<karl@gmx.de>   |
      | holidays        | <false>                 |
      | guestNames      | array<Miranda,Alex,Tom> |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type        | Walk            |
      | name         | This is my Walk |
      | teamName     | Westhang        |
      | isWithGuests | <false>         |
      | guestNames   | array<>         |
    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name            | isWithGuests | guestNames |
      | This is my Walk | <false>      | array<>    |

  @api @walkCreate
  Scenario: I can request /api/walks/create with a team who has isWithGuests
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                   |
      | team            | teamIri<CA>             |
      | name            | This is my Walk         |
      | conceptOfDay    | High and out.           |
      | weather         | Arschkalt               |
      | startTime       | 01.01.2020              |
      | walkTeamMembers | userIris<karl@gmx.de>   |
      | holidays        | <false>                 |
      | guestNames      | array<Miranda,Alex,Tom> |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type        | Walk                    |
      | name         | This is my Walk         |
      | teamName     | CA                      |
      | isWithGuests | <true>                  |
      | guestNames   | array<Alex,Miranda,Tom> |
    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name            | isWithGuests | guestNames              |
      | This is my Walk | <true>       | array<Alex,Miranda,Tom> |
