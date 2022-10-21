Feature: Testing wayPoint create resource with user groups

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email       | roles | client        |
      | karl@gmx.de |       | client@gmx.de |
    Given the following teams exists:
      | name     | users       | client        | isWithUserGroups | userGroups     |
      | Westhang | karl@gmx.de | client@gmx.de | <false>          |                |
      | CA       | karl@gmx.de | client@gmx.de | <true>           | Nutzende,Dudes |
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

  @api @wayPointCreate
  Scenario: I can request /api/way_points/create and will create a wayPoint for a team/walk with isWithUserGroups disabled
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/way_points/create" with parameters:
      | key               | value                                                         |
      | walk              | walkIri<Spaziergang>                                          |
      | locationName      | Assieck                                                       |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | imageFileName     | <null>                                                        |
      | imageFileData     | <null>                                                        |
      | contactsCount     | <null>                                                        |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
      | userGroups        | userGroups<Nutzende,7;Dudes,2>                                |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type        | WayPoint |
      | locationName | Assieck  |
      | userGroups   | array<>  |

    And I can find the following wayPoints in database:
      | locationName | userGroups |
      | Assieck      |            |
    And there are exactly 1 wayPoints in database


  @api @wayPointCreate
  Scenario: I can request /api/way_points/create and will create a wayPoint for a team/walk with isWithUserGroups enabled
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/way_points/create" with parameters:
      | key               | value                                                         |
      | walk              | walkIri<Gamescon>                                             |
      | locationName      | Ackis                                                         |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | imageFileName     | <null>                                                        |
      | imageFileData     | <null>                                                        |
      | contactsCount     | <null>                                                        |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
      | userGroups        | userGroups<Nutzende,7;Dudes,2>                                |
    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type                            | WayPoint |
      | locationName                     | Ackis    |
      | userGroups[0].userGroupName.name | Nutzende |
      | userGroups[0].peopleCount.count  | 7        |
      | userGroups[1].userGroupName.name | Dudes    |
      | userGroups[1].peopleCount.count  | 2        |

    And I can find the following wayPoints in database:
      | locationName | userGroups         |
      | Ackis        | Nutzende,7;Dudes,2 |
    And there are exactly 1 wayPoints in database
