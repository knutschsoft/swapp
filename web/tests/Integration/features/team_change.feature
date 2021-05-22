Feature: Testing team change resource

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

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key       | value            |
      | team      | teamIri<Empties> |
      | name      | Religion         |
      | ageRanges | ageRanges<>      |
      | users     | userIris<>       |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as authenticated user and will get access denied
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key       | value            |
      | team      | teamIri<Empties> |
      | name      | Religion         |
      | ageRanges | ageRanges<>      |
      | users     | userIris<>       |
    Then the response should be in JSON
    And the response status code should be 403
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an admin and change a team
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key       | value                |
      | team      | teamIri<Empties>     |
      | name      | Religion             |
      | ageRanges | ageRanges<1-3>       |
      | users     | userIris<two@pac.de> |
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | @type                      | Team       |
      | name                       | Religion   |
      | ageRanges[0].rangeStart    | 1          |
      | ageRanges[0].rangeEnd      | 3          |
      | ageRanges[0].frontendLabel | 1 - 3      |
      | users[0].email             | two@pac.de |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an superadmin and change a team
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key       | value                |
      | team      | teamIri<Empties>     |
      | name      | Religion             |
      | ageRanges | ageRanges<1-3>       |
      | users     | userIris<two@pac.de> |
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | name                       | Religion   |
      | name                       | Religion   |
      | ageRanges[0].rangeStart    | 1          |
      | ageRanges[0].rangeEnd      | 3          |
      | ageRanges[0].frontendLabel | 1 - 3      |
      | users[0].email             | two@pac.de |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an superadmin with empty color and name and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key  | value |
      | name |       |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | team                                                                   |
      | violations[0].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[1].propertyPath | name                                                                   |
      | violations[1].message      | Dieser Wert sollte nicht leer sein.                                    |
      | violations[2].propertyPath | name                                                                   |
      | violations[2].message      | Diese Zeichenkette ist zu kurz. Sie sollte mindestens 3 Zeichen haben. |
      | violations[3].propertyPath | users                                                                  |
      | violations[3].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[4].propertyPath | ageRanges                                                              |
      | violations[4].message      | Dieser Wert sollte nicht null sein.                                    |
