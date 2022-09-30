Feature: Testing walk create resource

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
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following teams exists:
      | name     | users                  | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
      | Como esta?     | gamer@gmx.de  |
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

  @api @walkCreate
  Scenario: I can request /api/walks/create as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and can not create a new walk when parameters are missing
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | name                                |
      | violations[0].message      | Dieser Wert sollte nicht leer sein. |
      | violations[1].propertyPath | name                                |
      | violations[1].message      | Dieser Wert sollte nicht null sein. |
      | violations[2].propertyPath | conceptOfDay                        |
      | violations[2].message      | Dieser Wert sollte nicht null sein. |
      | violations[3].propertyPath | weather                             |
      | violations[3].message      | Dieser Wert sollte nicht null sein. |
      | violations[4].propertyPath | startTime                           |
      | violations[4].message      | Dieser Wert sollte nicht null sein. |
      | violations[5].propertyPath | walkTeamMembers                     |
      | violations[5].message      | Dieser Wert sollte nicht null sein. |
      | violations[6].propertyPath | guestNames                          |
      | violations[6].message      | Dieser Wert sollte nicht null sein. |
    And there are exactly 2 walks in database

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and will create a new walk
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<Westhang>     |
      | name            | This is my Walk       |
      | conceptOfDay    | High and out.         |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk            |
      | name             | This is my Walk |
      | systemicQuestion | Esta muy bien?  |
      | weather          | Arschkalt       |
      | isUnfinished     | 1               |
      | teamName         | Westhang        |
    And there are exactly 3 walks in database

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and can not create a new walk with wrong team given
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<CA>           |
      | name            | This is my Walk       |
      | conceptOfDay    | High and out.         |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 400
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |
    And there are exactly 2 walks in database

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and can create a new walk for another user
    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                     |
      | team            | teamIri<Westhang>         |
      | name            | This is my Walk           |
      | conceptOfDay    | High and out.             |
      | weather         | Arschkalt                 |
      | startTime       | 2020-01-01T13:37:22+02:00 |
      | walkTeamMembers | userIris<karl@gmx.de>     |
      | holidays        | <false>                   |
      | guestNames      | array<>                   |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type            | Walk                      |
      | name             | This is my Walk           |
      | systemicQuestion | Esta muy bien?            |
      | weather          | Arschkalt                 |
      | isUnfinished     | 1                         |
      | teamName         | Westhang                  |
      | startTime        | 2020-01-01T13:37:22+02:00 |
      | endTime          | <null>                    |
    And there are exactly 3 walks in database

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and can create a new walk for another team
    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<CA>           |
      | name            | This is my Walk       |
      | conceptOfDay    | High and out.         |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk            |
      | name             | This is my Walk |
      | systemicQuestion | Esta muy bien?  |
      | weather          | Arschkalt       |
      | isUnfinished     | 1               |
      | teamName         | CA              |
    And there are exactly 3 walks in database

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and can not create a new walk when I am not part of a team
    Given I am authenticated against api as "lonely@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 400
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 403
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 403
    And the JSON nodes should be equal to:
      | @type             | hydra:Error       |
      | hydra:title       | An error occurred |
      | hydra:description | Access Denied.    |
    And there are exactly 2 walks in database

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and will create a new walk with walkTeamMembers assigned
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<Westhang>     |
      | name            | This is my Walk       |
      | conceptOfDay    | High and out.         |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 200
    And I can find the following walks in database:
      | name            | walkTeamMembers |
      | This is my Walk | karl@gmx.de     |
    And there are exactly 3 walks in database

  @api @walkCreate @security
  Scenario: I can request /api/walks/create as authenticated user with script tags in name and will create a new walk without script tags in name
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                                                         |
      | team            | teamIri<Westhang>                                             |
      | name            | \| <br><br><a href=“https:///www.google.com”>Google</a> holla |
      | conceptOfDay    | High and out.                                                 |
      | weather         | Arschkalt                                                     |
      | startTime       | 01.01.2020                                                    |
      | walkTeamMembers | userIris<karl@gmx.de>                                         |
      | holidays        | <false>                                                       |
      | guestNames      | array<>                                                       |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk            |
      | name             | \| Google holla |
      | systemicQuestion | Esta muy bien?  |
      | weather          | Arschkalt       |
      | isUnfinished     | 1               |
      | teamName         | Westhang        |
    And there are exactly 3 walks in database

  @api @walkCreate @systemicQuestion
  Scenario: I can request /api/walks/create as authenticated user of another client and will have this clients systemic question    Given I am authenticated against api as "karl@gmx.de"
    Given I am authenticated against api as "karl@gamer.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                   |
      | team            | teamIri<Gamers>         |
      | name            | MyName                  |
      | conceptOfDay    | High and out.           |
      | weather         | Arschkalt               |
      | startTime       | 01.01.2020              |
      | walkTeamMembers | userIris<karl@gamer.de> |
      | holidays        | <false>                 |
      | guestNames      | array<>                 |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk       |
      | name             | MyName     |
      | systemicQuestion | Como esta? |
      | weather          | Arschkalt  |
      | isUnfinished     | 1          |
      | teamName         | Gamers     |
    And there are exactly 3 walks in database

  @api @walkCreate @systemicQuestion
  Scenario: I can request /api/walks/create as authenticated user and can not create a walk with an user of another client
    Given I am authenticated against api as "karl@gamer.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<Gamers>       |
      | name            | myName                |
      | conceptOfDay    | High and out.         |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 400
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |
    And the JSON node 'hydra:description' should contain 'Item not found for '
    And the JSON node 'hydra:description' should contain 'api'
    And the JSON node 'hydra:description' should contain 'users'
    And there are exactly 2 walks in database

  @api @walkCreate
  Scenario: I can request /api/walks/create as authenticated user and can create a new walk for another user with full length fields
    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/walks/create" with parameters:
      | key             | value                 |
      | team            | teamIri<Westhang>     |
      | name            | string<300>           |
      | conceptOfDay    | string<2500>          |
      | weather         | Arschkalt             |
      | startTime       | 01.01.2020            |
      | walkTeamMembers | userIris<karl@gmx.de> |
      | holidays        | <false>               |
      | guestNames      | array<>               |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type            | Walk           |
      | name             | string<300>    |
      | systemicQuestion | Esta muy bien? |
      | weather          | Arschkalt      |
      | isUnfinished     | 1              |
      | teamName         | Westhang       |
      | conceptOfDay     | string<2500>   |
    And there are exactly 3 walks in database
