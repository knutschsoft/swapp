Feature: Testing way point resource

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
      | name        | team     |
      | Spaziergang | CA       |
      | Gorbitz     | Westhang |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |
      | Elbamare     | Gorbitz     |
      | Block17      | Gorbitz     |

  @api @wayPoint
  Scenario: I can request /api/way_points as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/way_points"
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @wayPoint
  Scenario: I can request /api/way_points as authenticated user and will see all way points
    Given I am authenticated against api as "karl@gmx.de"
    When I send a GET request to "/api/way_points"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems             | 3        |
      | hydra:member[0].@type        | WayPoint |
      | hydra:member[0].locationName | Assieck  |

    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/way_points"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 3 |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/way_points"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 3 |