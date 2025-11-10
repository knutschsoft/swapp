Feature: Testing walkConceptsOfDay resource

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
        Given the following walks exists:
            | name        | team     | conceptOfDay                       |
            | Spaziergang | CA       | array<1. Streetwork>               |
            | Gogo        | Westhang | array<1. Streetwork,3. Streetwork> |
            | Gamescon    | Gamers   | array<2. Streetwork>               |

    @api @walkConceptsOfDay
    Scenario: I can request /api/walks/concepts_of_day as a not authenticated user and an auth error will occur
        When I send a GET request to "/api/walks/concepts_of_day"
        Then the response should be in JSON
        And the response status code should be 401
#    And print last JSON response
        And the JSON nodes should be equal to:
            | code | 401 |

    @api @walkConceptsOfDay
    Scenario: I can request /api/walks/concepts_of_day as authenticated user and get a restricted result
        Given I am authenticated against api as "karl@gmx.de"
        When I send a GET request to "/api/walks/concepts_of_day"
        Then the response should be in JSON
#        And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems                | 2             |
            | member[0].conceptOfDay[0] | 1. Streetwork |
            | member[0].conceptOfDay[1] | 3. Streetwork |
            | member[1].conceptOfDay[0] | 1. Streetwork |

        Given I am authenticated against api as "lonely@gmx.de"
        When I send a GET request to "/api/walks/concepts_of_day"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 2 |

        Given I am authenticated against api as "karl@gamer.de"
        When I send a GET request to "/api/walks/concepts_of_day"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 1 |

        Given I am authenticated against api as "admin@gmx.de"
        When I send a GET request to "/api/walks/concepts_of_day"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 2 |

        Given I am authenticated against api as "superadmin@gmx.de"
        When I send a GET request to "/api/walks/concepts_of_day"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 3 |
