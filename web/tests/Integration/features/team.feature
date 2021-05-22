Feature: Testing team resource

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
      | Empties  |                        |                    |

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
      | hydra:member[0].users[0].email          | karl@gmx.de |

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
      | hydra:totalItems | 3 |
