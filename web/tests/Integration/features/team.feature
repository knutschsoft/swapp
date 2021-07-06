Feature: Testing team resource

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
      | Empties  |                        |                    | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |

  @api @team
  Scenario: I can request /api/teams as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/teams"
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @team
  Scenario: I can request /api/teams as authenticated user and get a restricted result
    Given I am authenticated against api as "karl@gmx.de"
    When I send a GET request to "/api/teams"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems                        | 1           |
      | hydra:member[0].@type                   | Team        |
      | hydra:member[0].name                    | Westhang    |
      | hydra:member[0].ageRanges[0].rangeStart | 1           |
      | hydra:member[0].ageRanges[0].rangeEnd   | 10          |
      | hydra:member[0].ageRanges[2].rangeEnd   | 13          |
      | hydra:member[0].ageRanges[2].rangeEnd   | 90          |
#      | hydra:member[0].users[0]                | userIri<karl@gmx.de> |

    Given I am authenticated against api as "lonely@gmx.de"
    When I send a GET request to "/api/teams"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 0 |

    Given I am authenticated against api as "two@pac.de"
    When I send a GET request to "/api/teams"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 2 |

    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/teams"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 3 |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/teams"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 4 |
