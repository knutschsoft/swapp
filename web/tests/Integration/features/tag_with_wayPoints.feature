Feature: Testing tag resource with exists filter to get all tags with wayPoints

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
        Given the following teams exists:
            | name     | users         | client        |
            | Westhang | karl@gmx.de   | client@gmx.de |
            | CA       | admin@gmx.de  | client@gmx.de |
            | Gamers   | karl@gamer.de | gamer@gmx.de  |
        Given the following tags exists:
            | name    | color     | client        |
            | Gewalt  | Chocolate | client@gmx.de |
            | Drogen  | Blue      | client@gmx.de |
            | Polizei | Brown     | client@gmx.de |
            | RPG     | Blue      | gamer@gmx.de  |
        Given the following walks exists:
            | name        | team   | ageRanges |
            | Spaziergang | CA     | 1-2,3-10  |
            | Gamescon    | Gamers | 1-2,3-10  |
        Given the following way points exists:
            | locationName | walkName    | tags           |
            | Assieck      | Spaziergang | Gewalt         |
            | Assieck2     | Spaziergang | Drogen         |
            | Assieck3     | Spaziergang | Drogen, Gewalt |

    @api @tagWithWayPoints
    Scenario: I can request /api/tags?exists[wayPoints]=1 as authenticated user and will see all tags with wayPoints
        Given I am authenticated against api as "karl@gmx.de"
        When I send a GET request to "/api/tags?exists[wayPoints]=1"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems      | 2         |
            | member[0].@type | Tag       |
            | member[0].name  | Gewalt    |
            | member[0].color | Chocolate |
            | member[1].name  | Drogen    |
            | member[1].color | Blue      |

    @api @tagWithWayPoints
    Scenario: I can request /api/tags?exists[wayPoints]=0 as authenticated user and will see all tags without wayPoints
        Given I am authenticated against api as "karl@gmx.de"
        When I send a GET request to "/api/tags?exists[wayPoints]=0"
        Then the response should be in JSON
#    And print last JSON response
        And the JSON nodes should be equal to:
            | totalItems      | 1       |
            | member[0].@type | Tag     |
            | member[0].name  | Polizei |
            | member[0].color | Brown   |
