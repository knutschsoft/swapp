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
      | name        | team |
      | Spaziergang | CA   |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |

  @api @walk
  Scenario: I can request /api/walks as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/walks"
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 401
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walk
  Scenario: I can request /api/walks as authenticated user and will see all way points
    Given I am authenticated against api as "karl@gmx.de"
    When I send a GET request to "/api/walks"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems      | 1           |
      | hydra:member[0].@type | Walk        |
      | hydra:member[0].name  | Spaziergang |

    Given I am authenticated against api as "lonely@gmx.de"
    When I send a GET request to "/api/walks"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 1 |

    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/walks"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 1 |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/walks"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 1 |
