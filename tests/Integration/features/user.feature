Feature: Testing user resource

  Background:
    Given the following users exists:
      | email             | roles            |
      | karl@gmx.de       |                  |
      | lonely@gmx.de     |                  |
      | two@pac.de        |                  |
      | dr@dre.de         |                  |
      | admin@gmx.de      | ROLE_ADMIN       |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN |
    Given the following teams exists:
      | name     | users                  | ageRanges          |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 |
      | CA       | two@pac.de,dr@dre.de   | 1-10,3-12, 13 - 90 |

  @api @user
  Scenario: I can request /api/users as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/users"
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code    | 401                 |

  @api @user
  Scenario: I can request /api/users as authenticated user and will only see users of my team
    Given I am authenticated against api as "karl@gmx.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems         | 2           |
      | hydra:member[0].@type    | User        |
      | hydra:member[0].username | karl@gmx.de |
      | hydra:member[1].username | two@pac.de  |

    Given I am authenticated against api as "lonely@gmx.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems         | 1             |
      | hydra:member[0].username | lonely@gmx.de |

    Given I am authenticated against api as "two@pac.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 3 |
      | hydra:member[0].username | karl@gmx.de |
      | hydra:member[1].username | two@pac.de  |
      | hydra:member[2].username | dr@dre.de  |

    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 6 |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 6 |
