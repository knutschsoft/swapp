Feature: Testing client change resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | main@gmx.de   |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |

  @api @apiClientChange
  Scenario: I can request /api/clients/change as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/clients/change" with parameters:
      | key         | value                    |
      | client      | clientIri<client@gmx.de> |
      | name        | Streetworkers            |
      | email       | newclient@example.com    |
      | description |                          |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @apiClientChange
  Scenario: I can request /api/clients/change as authenticated user and will get access denied cause I am not a superadmin
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/clients/change" with parameters:
      | key         | value                    |
      | client      | clientIri<client@gmx.de> |
      | name        | Streetworkers            |
      | email       | newclient@example.com    |
      | description |                          |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 403
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/clients/change" with parameters:
      | key         | value                    |
      | client      | clientIri<client@gmx.de> |
      | name        | Streetworkers            |
      | email       | newclient@example.com    |
      | description |                          |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 403
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

  @api @apiClientChange
  Scenario: I can request /api/clients/change as a superadmin and change a client
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/clients/change" with parameters:
      | key         | value                    |
      | client      | clientIri<client@gmx.de> |
      | name        | Streetworkers            |
      | email       | newclient@example.com    |
      | description | doing good stuff         |
    Then the response should be in JSON
#    And print last JSON response
#    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type             | Client                |
      | name              | Streetworkers         |
      | email             | newclient@example.com |
      | description       | doing good stuff      |
      | users[0]          | userIri<karl@gmx.de>  |
      | users[1]          | userIri<admin@gmx.de> |
      | teams             | array<>               |
      | ratingImageName   |                       |
#      | ratingImageSrc    | <null>                |
