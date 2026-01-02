Feature: A user can change his user preferences

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email         | client        |
            | karl@gmx.de   | client@gmx.de |
            | lonely@gmx.de | client@gmx.de |

    @userPreferences
    Scenario: I can change my userPreferences as an user

        Given I am authenticated against api as "karl@gmx.de"
        When I send an api platform GET request to "/api/user_preferences/userPreferencesId<lonely@gmx.de>" with parameters:
            | key | value |
        Then the response should be in JSON
        And the response status code should be 403

        Given I am authenticated against api as "lonely@gmx.de"
        When I send an api platform GET request to "/api/user_preferences/userPreferencesId<lonely@gmx.de>" with parameters:
            | key | value |
        Then the response should be in JSON
#        And print last JSON response
        And the enriched JSON nodes should be equal to:
            | id                                        | userPreferencesId<lonely@gmx.de> |
            | preferences.tables.wayPoints.columns.note | <true>                           |
            | preferences.tables.wayPoints.filters.note | <true>                           |
            | preferences.tables.walks.columns.name     | <true>                           |
            | preferences.tables.walks.filters.name     | <true>                           |

        Given I am authenticated against api as "karl@gmx.de"
        When I send an api platform POST request to "/api/user_preferences/change" with parameters:
            | key         | value                                                                           |
            | user        | userIri<lonely@gmx.de>                                                          |
            | preferences | json<{"tables":{"wayPoints":{"filters":{"walkName":false,"visitedAt":false}}}}> |
        Then the response should be in JSON
        And the response status code should be 400

        Given I am authenticated against api as "lonely@gmx.de"
        When I send an api platform POST request to "/api/user_preferences/change" with parameters:
            | key         | value                                                                           |
            | user        | userIri<lonely@gmx.de>                                                          |
            | preferences | json<{"tables":{"wayPoints":{"filters":{"walkName":false,"visitedAt":false}}}}> |
        Then the response should be in JSON
#        And print last JSON response
        And the enriched JSON nodes should be equal to:
            | id                                             | userPreferencesId<lonely@gmx.de> |
            | preferences.tables.wayPoints.columns.note      | <true>                           |
            | preferences.tables.wayPoints.filters.note      | <true>                           |
            | preferences.tables.wayPoints.filters.walkName  | <false>                          |
            | preferences.tables.wayPoints.filters.visitedAt | <false>                          |
            | preferences.tables.walks.columns.name          | <true>                           |
            | preferences.tables.walks.filters.name          | <true>                           |

        Given I am authenticated against api as "lonely@gmx.de"
        When I send an api platform POST request to "/api/user_preferences/change" with parameters:
            | key         | value                                                                         |
            | user        | userIri<lonely@gmx.de>                                                        |
            | preferences | json<{"tables":{"wayPoints":{"filters":{"walkName":true,"visitedAt":true}}}}> |
        Then the response should be in JSON
#        And print last JSON response
        And the enriched JSON nodes should be equal to:
            | id                                             | userPreferencesId<lonely@gmx.de> |
            | preferences.tables.wayPoints.columns.note      | <true>                           |
            | preferences.tables.wayPoints.filters.note      | <true>                           |
            | preferences.tables.wayPoints.filters.walkName  | <true>                           |
            | preferences.tables.wayPoints.filters.visitedAt | <true>                           |
            | preferences.tables.walks.columns.name          | <true>                           |
            | preferences.tables.walks.filters.name          | <true>                           |
