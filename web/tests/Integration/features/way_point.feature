Feature: Testing way point resource

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
      | name   | color | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue  | client@gmx.de |
    Given the following walks exists:
      | name        | team     |
      | Spaziergang | CA       |
      | Gorbitz     | Westhang |
      | Gamescon    | Gamers   |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |
      | Elbamare     | Gorbitz     |
      | Block17      | Gorbitz     |
      | BOTW         | Gamescon    |
      | BOTW2        | Gamescon    |

  @api @wayPoint
  Scenario: I can request /api/way_points as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/way_points"
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @wayPoint @suw
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
      | hydra:totalItems | 5 |
