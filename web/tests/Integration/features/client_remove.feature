Feature: Testing client delete resource

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
            | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
            | karl@gamer.de     |                  | gamer@gmx.de  |
            | pinky@gamer.de    |                  | gamer@gmx.de  |
            | admin@gamer.de    | ROLE_ADMIN       | gamer@gmx.de  |
            | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
        Given the following teams exists:
            | name     | users                        | ageRanges          | client        |
            | Westhang | karl@gmx.de,two@pac.de       | 1-10,3-12, 13 - 90 | client@gmx.de |
            | CA       | two@pac.de,lonely@gmx.de     | 1-10,3-12, 13 - 90 | client@gmx.de |
            | Gamers   | karl@gamer.de,pinky@gamer.de |                    | gamer@gmx.de  |
        Given the following systemic questions exists:
            | question       | client        |
            | Esta muy bien? | client@gmx.de |
            | Como te va?    | client@gmx.de |
            | How are you?   | gamer@gmx.de  |
        Given the following tags exists:
            | name        | color     | client        |
            | Gewalt      | Chocolate | client@gmx.de |
            | Drogen      | Blue      | client@gmx.de |
            | Kulleraugen | Blue      | gamer@gmx.de  |
        Given the following walks exists:
            | name        | team   | ageRanges | walkCreator         |
            | Spaziergang | CA     | 1-2,3-10  | user<lonely@gmx.de> |
            | Streifgang  | CA     | 1-2,3-10  | user<lonely@gmx.de> |
            | Gamescon    | Gamers | 1-2,3-10  |                     |
        Given the following way points exists:
            | locationName | walkName    | tags           |
            | Assieck      | Spaziergang | Drogen, Gewalt |
            | Assieck2     | Spaziergang | Drogen, Gewalt |
            | GamerSpot    | Gamescon    | Kulleraugen    |

    @api @client @remove
    Scenario: I can request /api/clients/remove as superadmin and delete a client with all related entities while leaving other clients untouched
        Given I am authenticated against api as "superadmin@gmx.de"
        And there are exactly 3 clients in database
        And there are exactly 8 users in database
        And there are exactly 3 teams in database
        And there are exactly 3 systemicQuestions in database
        And there are exactly 3 tags in database
        And there are exactly 3 walks in database
        And there are exactly 3 wayPoints in database
        And there are exactly 5 tagWayPoints in database
        And there are exactly 6 userWalks in database
        And I can find the following walks in database:
            | name        |
            | Spaziergang |
            | Streifgang  |
            | Gamescon    |

        When I send an api platform "POST" request to "/api/clients/remove" with parameters:
            | key    | value                    |
            | client | clientIri<client@gmx.de> |
        Then the response status code should be 200

        # Client gone
        And there are exactly 2 clients in database

        # Cascade: all walks of the client gone, gamer's walk stays
        And there are exactly 1 walks in database
        And I can not find the following walks in database:
            | name        |
            | Spaziergang |
            | Streifgang  |
        And I can find the following walks in database:
            | name     |
            | Gamescon |

        # Cascade: way points of the client gone, gamer way point stays
        And there are exactly 1 wayPoints in database
        And I can not find the following wayPoints in database:
            | locationName |
            | Assieck      |
            | Assieck2     |
        And I can find the following wayPoints in database:
            | locationName |
            | GamerSpot    |

        # Cascade: tags of the client gone (incl. their wayPoint relations), Kulleraugen stays
        And there are exactly 1 tags in database
        And there are exactly 1 tagWayPoints in database

        # Cascade: walkTeamMembers of removed walks gone, Gamescon membership intact
        And there are exactly 2 userWalks in database

        # Cascade: client users gone, foreign client users (gamer + main) stay
        And there are exactly 4 users in database
        And I can find the following users in database:
            | email             |
            | karl@gamer.de     |
            | pinky@gamer.de    |
            | admin@gamer.de    |
            | superadmin@gmx.de |

        # Cascade: client teams gone, gamer team stays
        And there are exactly 1 teams in database
        And I can find the following teams in database:
            | name   |
            | Gamers |

        # Cascade: client systemicQuestions gone, gamer one stays
        And there are exactly 1 systemicQuestions in database

    @api @client @remove
    Scenario: I can request /api/clients/remove as superadmin and walk image files of the deleted client are removed while files of other clients survive
        Given I am authenticated against api as "admin@gmx.de"
        # Upload an image to a way point of the soon-to-be-deleted client
        When I send an api platform "POST" request to "/api/way_points/change" with parameters:
            | key               | value                                                         |
            | wayPoint          | wayPointIri<Assieck>                                          |
            | locationName      | Assieck                                                       |
            | note              | High and out.                                                 |
            | oneOnOneInterview | Sonne                                                         |
            | isMeeting         | <false>                                                       |
            | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
            | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
            | imageFileName     | client.jpg                                                    |
            | imageFileData     | @image.jpg                                                    |
            | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
            | userGroups        | userGroups<>                                                  |
            | consumables       | consumables<>                                                 |
            | counselings       | counselings<>                                                 |
            | medicals          | medicals<>                                                    |
            | peopleCount       | int<0>                                                        |
        Then I can find the file "/images/way_points/timestamp<now>_client.jpg" in public folder

        Given I am authenticated against api as "admin@gamer.de"
        # Upload an image to a way point of the OTHER client (gamer) — must survive
        When I send an api platform "POST" request to "/api/way_points/change" with parameters:
            | key               | value                                                         |
            | wayPoint          | wayPointIri<GamerSpot>                                        |
            | locationName      | GamerSpot                                                     |
            | note              | gaming                                                        |
            | oneOnOneInterview |                                                               |
            | isMeeting         | <false>                                                       |
            | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
            | wayPointTags      | tagIris<Kulleraugen>                                          |
            | imageFileName     | gamer.jpg                                                     |
            | imageFileData     | @image.jpg                                                    |
            | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
            | userGroups        | userGroups<>                                                  |
            | consumables       | consumables<>                                                 |
            | counselings       | counselings<>                                                 |
            | medicals          | medicals<>                                                    |
            | peopleCount       | int<0>                                                        |
        Then I can find the file "/images/way_points/timestamp<now>_gamer.jpg" in public folder

        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/clients/remove" with parameters:
            | key    | value                    |
            | client | clientIri<client@gmx.de> |
        Then the response status code should be 200

        # File of deleted client gone, file of other client survives
        And I can not find the file "/images/way_points/timestamp<now>_client.jpg" in public folder
        And I can find the file "/images/way_points/timestamp<now>_gamer.jpg" in public folder

    @api @client @remove
    Scenario: I can not request /api/clients/remove as authenticated regular admin
        Given I am authenticated against api as "admin@gmx.de"
        And there are exactly 3 clients in database
        When I send an api platform "POST" request to "/api/clients/remove" with parameters:
            | key    | value                    |
            | client | clientIri<client@gmx.de> |
        Then the response status code should be 403
        And the JSON nodes should be equal to:
            | @type       | Error             |
            | title       | An error occurred |
            | description | Access Denied.    |
        # Nothing changed
        And there are exactly 3 clients in database
        And there are exactly 8 users in database
        And there are exactly 3 walks in database
        And there are exactly 3 wayPoints in database

    @api @client @remove
    Scenario: I can not request /api/clients/remove as authenticated regular user
        Given I am authenticated against api as "karl@gmx.de"
        When I send an api platform "POST" request to "/api/clients/remove" with parameters:
            | key    | value                    |
            | client | clientIri<client@gmx.de> |
        Then the response status code should be 403
        And the JSON nodes should be equal to:
            | @type       | Error             |
            | title       | An error occurred |
            | description | Access Denied.    |
        And there are exactly 3 clients in database

    @api @client @remove
    Scenario: I can not request /api/clients/remove for my own client (validator forbids self-removal)
        # superadmin@gmx.de belongs to main@gmx.de
        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/clients/remove" with parameters:
            | key    | value                  |
            | client | clientIri<main@gmx.de> |
        Then the response status code should be 422
        And the JSON nodes should be equal to:
            | violations[0].propertyPath | client                                                         |
            | violations[0].message      | Du kannst deinen eigenen Klienten "main@gmx.de" nicht löschen. |

        # Nothing changed
        And there are exactly 3 clients in database
        And there are exactly 8 users in database
        And there are exactly 3 walks in database
