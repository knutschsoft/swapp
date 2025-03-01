Feature: Testing wayPoint change resource with medicals

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email        | roles      | client        |
            | karl@gmx.de  |            | client@gmx.de |
            | admin@gmx.de | ROLE_ADMIN | client@gmx.de |
        Given the following teams exists:
            | name     | users       | client        | isWithMedicals | medicals                                             |
            | Westhang | karl@gmx.de | client@gmx.de | <false>        |                                                      |
            | CA       | karl@gmx.de | client@gmx.de | <true>         | Verband/Verbandswechsel,Sichtung/Präventionsberatung |
        Given the following systemic questions exists:
            | question       | client        |
            | Esta muy bien? | client@gmx.de |
        Given the following tags exists:
            | name   | color     | client        |
            | Gewalt | Chocolate | client@gmx.de |
            | Drogen | Blue      | client@gmx.de |
        Given the following walks exists:
            | name        | team     |
            | Spaziergang | Westhang |
            | Gamescon    | CA       |
        Given the following way points exists:
            | locationName | walkName    | medicals                                                 |
            | Assieck      | Spaziergang |                                                          |
            | Ackis        | Gamescon    | Verband/Verbandswechsel,7;Sichtung/Präventionsberatung,2 |

    @api @wayPoint
    Scenario: I can request /api/way_points/change and will change a wayPoint for a team/walk with isWithMedicals disabled
        Given I am authenticated against api as "admin@gmx.de"
        Given I can find the following wayPoints in database:
            | locationName | contactsCount |
            | Assieck      | <null>        |
        When I send an api platform "POST" request to "/api/way_points/change" with parameters:
            | key               | value                                                              |
            | wayPoint          | wayPointIri<Assieck>                                               |
            | locationName      | Assieck                                                            |
            | note              | High and out.                                                      |
            | oneOnOneInterview | Sonne                                                              |
            | isMeeting         | <false>                                                            |
            | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1>      |
            | wayPointTags      | tagIris<Gewalt,Drogen>                                             |
            | imageFileName     | <null>                                                             |
            | imageFileData     | <null>                                                             |
            | contactsCount     | <null>                                                             |
            | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                        |
            | userGroups        | userGroups<>                                                       |
            | consumables       | consumables<>                                                      |
            | counselings       | counselings<>                                                      |
            | medicals          | medicals<Verband/Verbandswechsel,7;Sichtung/Präventionsberatung,2> |
            | peopleCount       | int<0>                                                             |
#    And print last response
        Then the response status code should be 200
        And the enriched JSON nodes should be equal to:
            | @type        | WayPoint |
            | locationName | Assieck  |
            | medicals     | array<>  |

        And I can find the following wayPoints in database:
            | locationName | medicals |
            | Assieck      |          |
        And there are exactly 2 wayPoints in database

    @api @wayPoint
    Scenario: I can request /api/way_points/change and will change a wayPoint for a team/walk with isWithMedicals enabled
        Given I am authenticated against api as "admin@gmx.de"
        Given I can find the following wayPoints in database:
            | locationName | medicals                                                 |
            | Ackis        | Verband/Verbandswechsel,7;Sichtung/Präventionsberatung,2 |
        When I send an api platform "POST" request to "/api/way_points/change" with parameters:
            | key               | value                                                              |
            | wayPoint          | wayPointIri<Ackis>                                                 |
            | locationName      | Ackis                                                              |
            | note              | High and out.                                                      |
            | oneOnOneInterview | Sonne                                                              |
            | isMeeting         | <false>                                                            |
            | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1>      |
            | wayPointTags      | tagIris<Gewalt,Drogen>                                             |
            | imageFileName     | <null>                                                             |
            | imageFileData     | <null>                                                             |
            | contactsCount     | <null>                                                             |
            | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                        |
            | userGroups        | userGroups<>                                                       |
            | consumables       | consumables<>                                                      |
            | counselings       | counselings<>                                                      |
            | medicals          | medicals<Verband/Verbandswechsel,0;Sichtung/Präventionsberatung,8> |
            | peopleCount       | int<0>                                                             |
#    And print last response
        Then the response status code should be 200
        And the enriched JSON nodes should be equal to:
            | @type                         | WayPoint                     |
            | locationName                  | Ackis                        |
            | medicals[0].medicalName.name  | Verband/Verbandswechsel      |
            | medicals[0].peopleCount.count | 0                            |
            | medicals[1].medicalName.name  | Sichtung/Präventionsberatung |
            | medicals[1].peopleCount.count | 8                            |

        And I can find the following wayPoints in database:
            | locationName | medicals                                                 |
            | Ackis        | Verband/Verbandswechsel,0;Sichtung/Präventionsberatung,8 |
        And there are exactly 2 wayPoints in database
