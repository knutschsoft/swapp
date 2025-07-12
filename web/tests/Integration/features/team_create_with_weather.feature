Feature: Testing team create resource without weather

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email        | roles      | client        |
            | admin@gmx.de | ROLE_ADMIN | client@gmx.de |

    @api @apiTeamCreate @weather
    Scenario: I can request /api/teams/create as an admin and create a team without weather
        Given I am authenticated against api as "admin@gmx.de"
        When I send an api platform "POST" request to "/api/teams/create" with parameters:
            | key                     | value                             |
            | client                  | clientIri<client@gmx.de>          |
            | name                    | Religion                          |
            | ageRanges               | ageRanges<1-3>                    |
            | users                   | userIris<admin@gmx.de>            |
            | locationNames           | array<City, Spielplatz>           |
            | walkNames               | array<>                           |
            | conceptOfDaySuggestions | array<>                           |
            | isWithAgeRanges         | <true>                            |
            | isWithSystemicQuestion  | <true>                            |
            | isWithWeather           | <false>                           |
            | isWithHolidays          | <true>                            |
            | isWithPeopleCount       | <true>                            |
            | isWithContactsCount     | <false>                           |
            | isWithUserGroups        | <false>                           |
            | isWithConsumables       | <false>                           |
            | isWithCounselings       | <false>                           |
            | isWithMedicals          | <false>                           |
            | isWithGuests            | <true>                            |
            | guestNames              | array<Karla, Opa Manfred, Alfons> |
            | userGroupNames          | array<>                           |
            | consumableNames         | array<>                           |
            | counselingNames         | array<>                           |
            | medicalNames            | array<>                           |
            | initialMembersConfig    | mitglieder                        |
        Then the response should be in JSON
#    And print last JSON response
        And the response status code should be 200
        And the enriched JSON nodes should be equal to:
            | @type         | Team     |
            | name          | Religion |
            | isWithWeather | <false>  |

    @api @apiTeamCreate @weather
    Scenario: I can request /api/teams/create as an admin and create a team with weather
        Given I am authenticated against api as "admin@gmx.de"
        When I send an api platform "POST" request to "/api/teams/create" with parameters:
            | key                     | value                             |
            | client                  | clientIri<client@gmx.de>          |
            | name                    | Religion                          |
            | ageRanges               | ageRanges<1-3>                    |
            | users                   | userIris<admin@gmx.de>            |
            | locationNames           | array<City, Spielplatz>           |
            | walkNames               | array<>                           |
            | conceptOfDaySuggestions | array<>                           |
            | isWithAgeRanges         | <true>                            |
            | isWithSystemicQuestion  | <false>                           |
            | isWithWeather           | <true>                            |
            | isWithHolidays          | <true>                            |
            | isWithPeopleCount       | <true>                            |
            | isWithContactsCount     | <false>                           |
            | isWithUserGroups        | <false>                           |
            | isWithConsumables       | <false>                           |
            | isWithGuests            | <true>                            |
            | isWithCounselings       | <false>                           |
            | isWithMedicals          | <false>                           |
            | guestNames              | array<Karla, Opa Manfred, Alfons> |
            | userGroupNames          | array<>                           |
            | consumableNames         | array<>                           |
            | counselingNames         | array<>                           |
            | medicalNames            | array<>                           |
            | initialMembersConfig    | mitglieder                        |
        Then the response should be in JSON
        And print last JSON response
        And the response status code should be 200
        And the enriched JSON nodes should be equal to:
            | @type         | Team     |
            | name          | Religion |
            | isWithWeather | <true>   |
