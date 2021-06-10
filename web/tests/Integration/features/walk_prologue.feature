Feature: Testing walk prologue resource

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

  @api @walkPrologue
  Scenario: I can request /api/walks/prologue as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkPrologue
  Scenario: I can request /api/walks/prologue as authenticated user and will see all way points
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key    | value                    |
      | team   | teamIri<Westhang>        |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk           |
      | name             |                |
      | systemicQuestion | Esta muy bien? |
      | isUnfinished     | 1              |
      | teamName         | Westhang       |

    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key  | value       |
      | team | teamIri<CA> |
#    And print last response
    Then the response status code should be 400
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |

    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk           |
      | name             |                |
      | systemicQuestion | Esta muy bien? |
      | isUnfinished     | 1              |
      | teamName         | Westhang       |

    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key  | value       |
      | team | teamIri<CA> |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk           |
      | name             |                |
      | systemicQuestion | Esta muy bien? |
      | isUnfinished     | 1              |
      | teamName         | CA             |

    Given I am authenticated against api as "lonely@gmx.de"
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 400
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 403
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/prologue" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 403
    And the JSON nodes should be equal to:
      | @type             | hydra:Error       |
      | hydra:title       | An error occurred |
      | hydra:description | Access Denied.    |

    And there are exactly 5 walks in database
