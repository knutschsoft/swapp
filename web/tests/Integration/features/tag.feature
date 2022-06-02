Feature: Testing tag resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
      | main@gmx.de   |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
      | RPG    | Blue      | gamer@gmx.de  |

  @api @tag
  Scenario: I can request /api/tags as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/tags"
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @tag
  Scenario: I can request /api/tags as authenticated user and will see all tags
    Given I am authenticated against api as "karl@gmx.de"
    When I send a GET request to "/api/tags"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems      | 2         |
      | hydra:member[0].@type | Tag       |
      | hydra:member[0].name  | Gewalt    |
      | hydra:member[0].color | Chocolate |
      | hydra:member[1].name  | Drogen    |
      | hydra:member[1].color | Blue      |

    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/tags"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 2 |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/tags"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 3 |
