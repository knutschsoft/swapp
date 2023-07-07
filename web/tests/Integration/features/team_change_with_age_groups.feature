Feature: Testing team change resource with guests

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | roles      | client        |
      | admin@gmx.de | ROLE_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users        | ageRanges          | client        | isWithGuests |
      | Westhang | admin@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de | <true>       |

  @api @apiTeamChange @ageGroups
  Scenario: I can request /api/teams/change as an admin and can not change a team without ageGroups
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                     | value                     |
      | team                    | teamIri<Westhang>         |
      | name                    | Religion                  |
      | ageRanges               | ageRanges<>               |
      | users                   | userIris<admin@gmx.de>    |
      | locationNames           | array<City, Spielplatz>   |
      | walkNames               | array<>                   |
      | conceptOfDaySuggestions | array<>                   |
      | isWithAgeRanges         | <true>                    |
      | isWithSystemicQuestion  | <true>                    |
      | isWithPeopleCount       | <true>                    |
      | isWithContactsCount     | <false>                   |
      | isWithUserGroups        | <false>                   |
      | isWithGuests            | <true>                    |
      | guestNames              | array<Opa Manfred, Karla> |
      | userGroupNames          | array<>                   |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | altersgruppen                                                                                |
      | violations[0].message      | Das Team muss mindestens eine Altersgruppe haben, wenn die Altersgruppen mit erfasst werden. |

  @api @apiTeamChange @ageGroups
  Scenario: I can request /api/teams/change as an admin and can not change a team with ageGroups enabled and isWithPeopleCount disabled
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/teams/change" with parameters:
      | key                     | value                     |
      | team                    | teamIri<Westhang>         |
      | name                    | Religion                  |
      | ageRanges               | ageRanges<1-3>            |
      | users                   | userIris<admin@gmx.de>    |
      | locationNames           | array<City, Spielplatz>   |
      | walkNames               | array<>                   |
      | conceptOfDaySuggestions | array<>                   |
      | isWithAgeRanges         | <true>                    |
      | isWithSystemicQuestion  | <true>                    |
      | isWithPeopleCount       | <false>                   |
      | isWithContactsCount     | <false>                   |
      | isWithUserGroups        | <false>                   |
      | isWithGuests            | <true>                    |
      | guestNames              | array<Opa Manfred, Karla> |
      | userGroupNames          | array<>                   |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | anzahlPersonenVorOrt                                                                                |
      | violations[0].message      | Das Team muss "Anzahl Personen vor Ort" aktiviert haben, wenn die Altersgruppen mit erfasst werden. |
