Feature: Testing user resource

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
      | dr@dre.de         |                  | client@gmx.de |
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following teams exists:
      | name     | users                  | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de,dr@dre.de   | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |

  @api @user
  Scenario: I can request /api/users as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/users"
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

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
      | hydra:totalItems         | 3           |
      | hydra:member[0].username | karl@gmx.de |
      | hydra:member[1].username | two@pac.de  |
      | hydra:member[2].username | dr@dre.de   |

    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 5 |

    Given I am authenticated against api as "karl@gamer.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 1 |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/users"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 7 |
