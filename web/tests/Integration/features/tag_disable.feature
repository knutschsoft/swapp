Feature: Testing tag disable resource

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
      | Drogen | Ivory     | client@gmx.de |
      | RPG    | Blue      | gamer@gmx.de  |

  @api @apiTagDisable
  Scenario: I can request /api/tags/disable as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/tags/disable" with parameters:
      | key | value          |
      | tag | tagIri<Gewalt> |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @apiTagDisable
  Scenario: I can request /api/tags/disable as authenticated user and will get access denied
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/tags/disable" with parameters:
      | key | value          |
      | tag | tagIri<Gewalt> |
    Then the response should be in JSON
    And the response status code should be 403
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

  @api @apiTagDisable
  Scenario: I can request /api/tags/disable as an admin for another client and will get access denied
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/disable" with parameters:
      | key | value       |
      | tag | tagIri<RPG> |
#    And print last JSON response
    Then the response should be in JSON
    And the response status code should be 400
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |

  @api @apiTagDisable
  Scenario: I can request /api/tags/disable as an admin and disable a tag
    And I can find the following tags in database:
      | name   | isEnabled |
      | Gewalt | 1         |
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/disable" with parameters:
      | key | value          |
      | tag | tagIri<Gewalt> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | name      | Gewalt  |
      | isEnabled | <false> |
    And I can find the following tags in database:
      | name   | isEnabled |
      | Gewalt | 0         |

  @api @apiTagDisable
  Scenario: I can request /api/tags/disable as an superadmin and will disable a  tag
    And I can find the following tags in database:
      | name   | isEnabled |
      | Gewalt | 1         |
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/disable" with parameters:
      | key | value          |
      | tag | tagIri<Gewalt> |
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | name      | Gewalt  |
      | isEnabled | <false> |
    And I can find the following tags in database:
      | name   | isEnabled |
      | Gewalt | 0         |
