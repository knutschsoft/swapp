Feature: Testing team create resource

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
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |

  @api @apiTeamCreate
  Scenario: I can request /api/teams/create as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/teams/create" with parameters:
      | key                 | value                    |
      | client              | clientIri<client@gmx.de> |
      | name                | Religion                 |
      | ageRanges           | ageRanges<>              |
      | users               | userIris<>               |
      | isWithContactsCount | <false>                  |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @apiTeamCreate
  Scenario: I can request /api/teams/create as authenticated user and will get access denied cause I am not an admin
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/teams/create" with parameters:
      | key                 | value                    |
      | client              | clientIri<client@gmx.de> |
      | name                | Religion                 |
      | ageRanges           | ageRanges<>              |
      | users               | userIris<>               |
      | isWithContactsCount | <false>                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 403
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

  @api @apiTeamCreate
  Scenario: I can request /api/teams/create as an admin and create a team
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/create" with parameters:
      | key                 | value                    |
      | client              | clientIri<client@gmx.de> |
      | name                | Religion                 |
      | ageRanges           | ageRanges<1-3>           |
      | users               | userIris<two@pac.de>     |
      | locationNames       | array<City, Spielplatz>  |
      | isWithContactsCount | <false>                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type                      | Team                     |
      | name                       | Religion                 |
      | ageRanges[0].rangeStart    | 1                        |
      | ageRanges[0].rangeEnd      | 3                        |
      | ageRanges[0].frontendLabel | 1 - 3                    |
      | locationNames[0]           | City                     |
      | locationNames[1]           | Spielplatz               |
      | isWithContactsCount        | <false>                  |
      | users[0]                   | userIri<two@pac.de>      |
      | client                     | clientIri<client@gmx.de> |

  @api @apiTeamCreate
  Scenario: I can request /api/teams/create as an superadmin and create a team
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/create" with parameters:
      | key                 | value                    |
      | client              | clientIri<client@gmx.de> |
      | name                | Religion                 |
      | ageRanges           | ageRanges<1-3>           |
      | users               | userIris<two@pac.de>     |
      | locationNames       | array<City, Spielplatz>  |
      | isWithContactsCount | <true>                   |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | name                       | Religion                 |
      | name                       | Religion                 |
      | ageRanges[0].rangeStart    | 1                        |
      | ageRanges[0].rangeEnd      | 3                        |
      | ageRanges[0].frontendLabel | 1 - 3                    |
      | locationNames[0]           | City                     |
      | locationNames[1]           | Spielplatz               |
      | isWithContactsCount        | <true>                   |
      | users[0]                   | userIri<two@pac.de>      |
      | client                     | clientIri<client@gmx.de> |

  @api @apiTeamCreate
  Scenario: I can request /api/teams/create as an superadmin and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/create" with parameters:
      | key    | value                    |
      | name   |                          |
      | client | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | name                                                                   |
      | violations[0].message      | Dieser Wert sollte nicht leer sein.                                    |
      | violations[1].propertyPath | name                                                                   |
      | violations[1].message      | Diese Zeichenkette ist zu kurz. Sie sollte mindestens 3 Zeichen haben. |
      | violations[2].propertyPath | locationNames                                                          |
      | violations[2].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[3].propertyPath | users                                                                  |
      | violations[3].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[4].propertyPath | ageRanges                                                              |
      | violations[4].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[5].propertyPath | isWithContactsCount                                                    |
      | violations[5].message      | Dieser Wert sollte nicht null sein.                                    |

  @api @apiTeamCreate
  Scenario: I can request /api/teams/create as an admin of another client/team and can not create a team
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/create" with parameters:
      | key                 | value                   |
      | client              | clientIri<gamer@gmx.de> |
      | name                | Religion                |
      | ageRanges           | ageRanges<1-3>          |
      | users               | userIris<two@pac.de>    |
      | isWithContactsCount | <true>                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 400
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |