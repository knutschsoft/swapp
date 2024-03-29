Feature: Testing team change resource

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
    Given the following teams exists:
      | name     | users                  | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Empties  |                        |                    | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                 | value            |
      | team                | teamIri<Empties> |
      | name                | Religion         |
      | ageRanges           | ageRanges<>      |
      | users               | userIris<>       |
      | isWithContactsCount | <false>          |
      | isWithUserGroups    | <false>          |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as authenticated user and will get access denied
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                 | value             |
      | team                | teamIri<Westhang> |
      | name                | Religion          |
      | ageRanges           | ageRanges<>       |
      | users               | userIris<>        |
      | isWithContactsCount | <false>           |
      | isWithUserGroups    | <false>           |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 403
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an admin and change a team
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                     | value                   |
      | team                    | teamIri<Empties>        |
      | name                    | Religion                |
      | ageRanges               | ageRanges<1-3>          |
      | users                   | userIris<two@pac.de>    |
      | locationNames           | array<City, Spielplatz> |
      | walkNames               | array<>                 |
      | conceptOfDaySuggestions | array<>                 |
      | isWithAgeRanges         | <true>                  |
      | isWithSystemicQuestion  | <true>                  |
      | isWithPeopleCount       | <true>                  |
      | isWithContactsCount     | <false>                 |
      | isWithUserGroups        | <false>                 |
      | isWithGuests            | <false>                 |
      | guestNames              | array<>                 |
      | userGroupNames          | array<>                 |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type                      | Team                |
      | name                       | Religion            |
      | ageRanges[0].rangeStart    | 1                   |
      | ageRanges[0].rangeEnd      | 3                   |
      | ageRanges[0].frontendLabel | 1 - 3               |
      | locationNames[0]           | City                |
      | locationNames[1]           | Spielplatz          |
      | isWithAgeRanges            | <true>              |
      | isWithContactsCount        | <false>             |
      | isWithUserGroups           | <false>             |
      | users[0]                   | userIri<two@pac.de> |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an superadmin and change a team
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                     | value                          |
      | team                    | teamIri<Empties>               |
      | name                    | Religion                       |
      | ageRanges               | ageRanges<1-3>                 |
      | users                   | userIris<two@pac.de>           |
      | locationNames           | array<City, Spielplatz>        |
      | walkNames               | array<>                        |
      | conceptOfDaySuggestions | array<>                        |
      | isWithAgeRanges         | <true>                         |
      | isWithSystemicQuestion  | <true>                         |
      | isWithPeopleCount       | <true>                         |
      | isWithContactsCount     | <true>                         |
      | isWithUserGroups        | <true>                         |
      | userGroupNames          | userGroupNames<Nutzende,Dudes> |
      | isWithGuests            | <false>                        |
      | guestNames              | array<>                        |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | name                       | Religion            |
      | ageRanges[0].rangeStart    | 1                   |
      | ageRanges[0].rangeEnd      | 3                   |
      | ageRanges[0].frontendLabel | 1 - 3               |
      | locationNames[0]           | City                |
      | locationNames[1]           | Spielplatz          |
      | isWithAgeRanges            | <true>              |
      | isWithContactsCount        | <true>              |
      | isWithUserGroups           | <true>              |
      | users[0]                   | userIri<two@pac.de> |
      | userGroupNames[0].name     | Nutzende            |
      | userGroupNames[1].name     | Dudes               |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an superadmin and will get a validation error
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key  | value            |
      | name |                  |
      | team | teamIri<Empties> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath  | name                                                                   |
      | violations[0].message       | Dieser Wert sollte nicht leer sein.                                    |
      | violations[1].propertyPath  | name                                                                   |
      | violations[1].message       | Diese Zeichenkette ist zu kurz. Sie sollte mindestens 3 Zeichen haben. |
      | violations[2].propertyPath  | guestNames                                                             |
      | violations[2].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[3].propertyPath  | locationNames                                                          |
      | violations[3].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[4].propertyPath  | walkNames                                                              |
      | violations[4].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[5].propertyPath  | conceptOfDaySuggestions                                                |
      | violations[5].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[6].propertyPath  | users                                                                  |
      | violations[6].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[7].propertyPath  | isWithAgeRanges                                                        |
      | violations[7].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[8].propertyPath  | isWithPeopleCount                                                      |
      | violations[8].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[9].propertyPath  | ageRanges                                                              |
      | violations[9].message       | Dieser Wert sollte nicht null sein.                                    |
      | violations[10].propertyPath | isWithGuests                                                           |
      | violations[10].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[11].propertyPath | isWithSystemicQuestion                                                 |
      | violations[11].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[12].propertyPath | isWithContactsCount                                                    |
      | violations[12].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[13].propertyPath | isWithUserGroups                                                       |
      | violations[13].message      | Dieser Wert sollte nicht null sein.                                    |
      | violations[14].propertyPath | userGroupNames                                                         |
      | violations[14].message      | Dieser Wert sollte nicht null sein.                                    |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an admin and change isWithAgeRanges to false and the ageRanges are not changed
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                     | value                   |
      | team                    | teamIri<Empties>        |
      | name                    | Religion                |
      | ageRanges               | ageRanges<1-3>          |
      | users                   | userIris<two@pac.de>    |
      | locationNames           | array<City, Spielplatz> |
      | walkNames               | array<>                 |
      | conceptOfDaySuggestions | array<>                 |
      | isWithAgeRanges         | <false>                 |
      | isWithSystemicQuestion  | <true>                  |
      | isWithPeopleCount       | <false>                 |
      | isWithContactsCount     | <false>                 |
      | isWithUserGroups        | <false>                 |
      | isWithGuests            | <false>                 |
      | guestNames              | array<>                 |
      | userGroupNames          | array<>                 |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type               | Team                     |
      | name                | Religion                 |
      | locationNames[0]    | City                     |
      | locationNames[1]    | Spielplatz               |
      | isWithAgeRanges     | <false>                  |
      | isWithContactsCount | <false>                  |
      | isWithUserGroups    | <false>                  |
      | users[0]            | userIri<two@pac.de>      |
      | client              | clientIri<client@gmx.de> |
      | isWithGuests        | <false>                  |
      | guestNames          | array<>                  |
    And the JSON node "ageRanges" should exist
    And the JSON node "ageRanges[0]" should not exist

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an admin of another client/team and can not change a team
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                 | value                |
      | team                | teamIri<Gamers>      |
      | name                | Religion             |
      | ageRanges           | ageRanges<1-3>       |
      | users               | userIris<two@pac.de> |
      | isWithContactsCount | <true>               |
      | isWithUserGroups    | <true>               |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 400
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |

  @api @apiTeamChange
  Scenario: I can request /api/teams/change as an admin with an user of another client and can not change a team
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                 | value                   |
      | team                | teamIri<Empties>        |
      | name                | Religion                |
      | ageRanges           | ageRanges<1-3>          |
      | users               | userIris<karl@gamer.de> |
      | isWithContactsCount | <true>                  |
      | isWithUserGroups    | <true>                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 400
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |
