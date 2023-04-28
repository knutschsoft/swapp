Feature: Testing walk create resource with user groups feature

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email       | roles | client        |
      | karl@gmx.de |       | client@gmx.de |
    Given the following teams exists:
      | name     | users       | ageRanges          | client        | isWithUserGroups |
      | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | <false>          |
      | CA       | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | <true>           |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |

  @api @walkCreate
  Scenario: I can request /api/walks/create with a team who has isWithUserGroups disabled
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<Westhang>     |
      | name            | This is my Walk       |
      | conceptOfDay    | array<High and out.>  |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type            | Walk            |
      | name             | This is my Walk |
      | teamName         | Westhang        |
      | isWithUserGroups | <false>         |
    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name            | isWithUserGroups |
      | This is my Walk | <false>          |

  @api @walkCreate
  Scenario: I can request /api/walks/create with a team who has isWithUserGroups enabled
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<CA>           |
      | name            | This is my Walk       |
      | conceptOfDay    | array<High and out.>  |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type            | Walk            |
      | name             | This is my Walk |
      | teamName         | CA              |
      | isWithUserGroups | <true>          |
    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name            | isWithUserGroups |
      | This is my Walk | <true>           |
