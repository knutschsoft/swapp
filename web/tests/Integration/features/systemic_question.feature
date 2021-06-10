Feature: Testing systemic question resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
      | main@gmx.de   |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
      | Esta muy bien? | gamer@gmx.de  |

  @api @systemicQuestion
  Scenario: I can request /api/systemic_questions as a not authenticated user and an auth error will occur
    When I send a GET request to "/api/systemic_questions"
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @systemicQuestion
  Scenario: I can request /api/systemic_questions as authenticated user and will see all systemic questions
    Given I am authenticated against api as "karl@gmx.de"
    When I send a GET request to "/api/systemic_questions"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems         | 1                |
      | hydra:member[0].@type    | SystemicQuestion |
      | hydra:member[0].question | Esta muy bien?   |

    Given I am authenticated against api as "karl@gamer.de"
    When I send a GET request to "/api/systemic_questions"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 1 |

    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/systemic_questions"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 1 |

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/systemic_questions"
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems | 2 |
