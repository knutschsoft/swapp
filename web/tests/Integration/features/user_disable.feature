Feature: Testing user disable resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
      | main@gmx.de   |
    Given the following users exists:
      | email             | roles            | client        | isEnabled |
      | karl@gmx.de       |                  | client@gmx.de | 0         |
      | two@pac.de        |                  | client@gmx.de | 1         |
      | karl@gamer.de     |                  | gamer@gmx.de  | 0         |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de | 1         |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   | 1         |
    Given the following teams exists:
      | name     | users                  | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |

  @api @user
  Scenario: I can request /api/users/disable as a not authenticated user and an auth error will occur

    When I send an api platform POST request to "/api/users/disable" with parameters:
      | key  | value                |
      | user | userIri<karl@gmx.de> |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @user
  Scenario: I can request /api/users/disable as authenticated user
    Given I am authenticated against api as "two@pac.de"
    When I send an api platform POST request to "/api/users/disable" with parameters:
      | key  | value                |
      | user | userIri<two@pac.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

    Given I am authenticated against api as "admin@gmx.de"

    When I send an api platform POST request to "/api/users/disable" with parameters:
      | key  | value                  |
      | user | userIri<karl@gamer.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON node "hydra:description" should match "/Item not found for *./"

  @api @user
  Scenario: I can request /api/users/disable as authenticated user

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform POST request to "/api/users/disable" with parameters:
      | key  | value                |
      | user | userIri<karl@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred                          |
      | hydra:description          | userEnabled: Dieser Wert sollte true sein. |
      | violations[0].propertyPath | userEnabled                                |
      | violations[0].message      | Dieser Wert sollte true sein.              |

    Given I am authenticated against api as "superadmin@gmx.de"

    When I send an api platform POST request to "/api/users/disable" with parameters:
      | key  | value                |
      | user | userIri<karl@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred                          |
      | hydra:description          | userEnabled: Dieser Wert sollte true sein. |
      | violations[0].propertyPath | userEnabled                                |
      | violations[0].message      | Dieser Wert sollte true sein.              |

  @api @user
  Scenario: I can request /api/users/disable as authenticated user

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform POST request to "/api/users/disable" with parameters:
      | key  | value               |
      | user | userIri<two@pac.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | email   | two@pac.de |
      | enabled | 0      |

  @api @user
  Scenario: I can request /api/users/disable as authenticated user
    Given I am authenticated against api as "superadmin@gmx.de"

    When I send an api platform POST request to "/api/users/disable" with parameters:
      | key  | value               |
      | user | userIri<two@pac.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | email   | two@pac.de |
      | enabled | 0      |
