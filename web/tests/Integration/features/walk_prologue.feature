Feature: Testing walk prologue resource

  Background:
    Given the following users exists:
      | email             | roles            |
      | karl@gmx.de       |                  |
      | lonely@gmx.de     |                  |
      | two@pac.de        |                  |
      | admin@gmx.de      | ROLE_ADMIN       |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN |
    Given the following teams exists:
      | name     | users                  | ageRanges          |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 |
    Given the following systemic questions exists:
      | question       |
      | Esta muy bien? |
    Given the following tags exists:
      | name   | color |
      | Gewalt | Green |
      | Drogen | Blue  |
    Given the following walks exists:
      | name        | team |
      | Spaziergang | CA   |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |

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
    Then the response status code should be 400
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

    And there are exactly 4 walks in database
