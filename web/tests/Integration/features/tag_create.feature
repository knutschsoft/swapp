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
      | Drogen | Ivory     | client@gmx.de |
      | RPG    | Blue      | gamer@gmx.de  |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   | Religion                 |
      | color  | PowderBlue               |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as authenticated user and will get access denied
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   | Religion                 |
      | color  | PowderBlue               |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
    And the response status code should be 403
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an admin for another client and will get access denied
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                   |
      | name   | Religion                |
      | color  | PowderBlue              |
      | client | clientIri<gamer@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 400
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an admin and will get access denied
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   | Religion                 |
      | color  | PowderBlue               |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | name  | Religion   |
      | color | PowderBlue |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an superadmin and will create a new tag
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   | Religion                 |
      | color  | PowderBlue               |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | name  | Religion   |
      | color | PowderBlue |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an superadmin with invalid color and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   | Religion                 |
      | color  | Not a Color              |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | color                                       |
      | violations[0].message      | Sie haben einen ungültigen Wert ausgewählt. |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an superadmin with empty color and name and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   |                          |
      | color  |                          |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | color                               |
      | violations[0].message      | Dieser Wert sollte nicht leer sein. |
      | violations[0].propertyPath | name                                |
      | violations[0].message      | Dieser Wert sollte nicht leer sein. |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an superadmin with nulled color and name and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   | <null>                   |
      | color  | <null>                   |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 400
    And the JSON nodes should be equal to:
      | hydra:description | The input data is misformatted. |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an superadmin with unset color and name and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | color                               |
      | violations[0].message      | Dieser Wert sollte nicht leer sein. |
      | violations[0].propertyPath | name                                |
      | violations[0].message      | Dieser Wert sollte nicht leer sein. |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an superadmin with already existing tag for this client and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                    |
      | name   | Drogen                   |
      | color  | Ivory                    |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath |                                                                         |
      | violations[0].message      | Ein Tag mit dem Namen "Drogen" und der Farbe "Ivory" existiert bereits. |

  @api @apiTagCreate
  Scenario: I can request /api/tags/create as an superadmin with already existing name and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/tags/create" with parameters:
      | key    | value                   |
      | name   | Drogen                  |
      | color  | Ivory                   |
      | client | clientIri<gamer@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the JSON nodes should be equal to:
      | name  | Drogen |
      | color | Ivory  |
