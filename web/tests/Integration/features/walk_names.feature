Feature: Testing walkNames resource

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
            | name        | team     |
            | Spaziergang | CA       |
            | Gogo        | Westhang |
            | Gamescon    | Gamers   |
        Given the following way points exists:
            | locationName | walkName    |
            | Assieck      | Spaziergang |

    @api @walkNames
    Scenario: I can request /api/walks/walk_names as a not authenticated user and an auth error will occur
        When I send a GET request to "/api/walks/walk_names"
        Then the response should be in JSON
        And the response status code should be 401
#    And print last JSON response
        And the JSON nodes should be equal to:
            | code | 401 |

    @api @walkNames
    Scenario: I can request /api/walks/walk_names as authenticated user and get a restricted result
        Given I am authenticated against api as "karl@gmx.de"
        When I send a GET request to "/api/walks/walk_names?exists[wayPoints]=false"
        Then the response should be in JSON
#        And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 1 |
        Given I am authenticated against api as "karl@gmx.de"
        When I send a GET request to "/api/walks/walk_names?exists[wayPoints]=true"
        Then the response should be in JSON
#        And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 1 |

    @api @walkNames
    Scenario: I can request /api/walks/walk_names as authenticated user and get a restricted result
        Given I am authenticated against api as "karl@gmx.de"
        When I send a GET request to "/api/walks/walk_names"
        Then the response should be in JSON
#        And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems     | 2           |
            | member[0].name | Gogo        |
            | member[1].name | Spaziergang |

        Given I am authenticated against api as "lonely@gmx.de"
        When I send a GET request to "/api/walks/walk_names"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 2 |

        Given I am authenticated against api as "karl@gamer.de"
        When I send a GET request to "/api/walks/walk_names"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 1 |

        Given I am authenticated against api as "admin@gmx.de"
        When I send a GET request to "/api/walks/walk_names"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 2 |

        Given I am authenticated against api as "superadmin@gmx.de"
        When I send a GET request to "/api/walks/walk_names"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems | 3 |
