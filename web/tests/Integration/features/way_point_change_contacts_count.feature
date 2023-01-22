Feature: Testing wayPoint change resource with contacts count

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email        | roles      | client        |
      | karl@gmx.de  |            | client@gmx.de |
      | admin@gmx.de | ROLE_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users       | client        | isWithContactsCount |
      | Westhang | karl@gmx.de | client@gmx.de | <false>             |
      | CA       | karl@gmx.de | client@gmx.de | <true>              |
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
      | locationName | walkName    | contactsCount |
      | Assieck      | Spaziergang | <null>        |
      | Ackis        | Gamescon    | int<7>        |

  @api @wayPoint
  Scenario: I can request /api/way_points/change and will change a wayPoint for a team/walk with isWithContactsCount disabled
    Given I am authenticated against api as "admin@gmx.de"
    Given I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Assieck      | <null>        |
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Assieck>                                          |
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
      | userGroups        | userGroups<>                                                  |
      | peopleCount       | int<0>                                                        |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type         | WayPoint |
      | locationName  | Assieck  |
      | contactsCount | <null>   |

    And I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Assieck      | <null>        |
    And there are exactly 2 wayPoints in database

  @api @wayPoint
  Scenario: I can request /api/way_points/change and will not be able to set contactsCount of a wayPoint for a team/walk with isWithContactsCount disabled
    Given I am authenticated against api as "admin@gmx.de"
    Given I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Assieck      | <null>        |
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Assieck>                                          |
      | locationName      | Assieck                                                       |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | imageFileName     | <null>                                                        |
      | imageFileData     | <null>                                                        |
      | contactsCount     | int<7>                                                        |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
      | userGroups        | userGroups<>                                                  |
      | peopleCount       | int<0>                                                        |
#    And print last response
    Then the response status code should be 422
    And the enriched JSON nodes should be equal to:
      | hydra:title                | An error occurred                                     |
      | violations[0].propertyPath | contactsCount                                         |
      | violations[0].message      | Die Anzahl direkter Kontakte darf nicht gesetzt sein. |

    And I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Assieck      | <null>        |
    And there are exactly 2 wayPoints in database

  @api @wayPoint
  Scenario: I can request /api/way_points/change and will change a wayPoint for a team/walk with isWithContactsCount enabled
    Given I am authenticated against api as "admin@gmx.de"
    Given I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Ackis        | int<7>        |
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Ackis>                                            |
      | locationName      | Ackis                                                         |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | imageFileName     | <null>                                                        |
      | imageFileData     | <null>                                                        |
      | contactsCount     | int<22>                                                       |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
      | userGroups        | userGroups<>                                                  |
      | peopleCount       | int<0>                                                        |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type         | WayPoint |
      | locationName  | Ackis    |
      | contactsCount | int<22>  |

    And I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Ackis        | int<22>       |
    And there are exactly 2 wayPoints in database

  @api @wayPoint
  Scenario: I can request /api/way_points/change and will not be able to unset contactsCount of a wayPoint for a team/walk with isWithContactsCount enabled
    Given I am authenticated against api as "admin@gmx.de"
    Given I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Ackis        | int<7>        |
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Ackis>                                            |
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
      | userGroups        | userGroups<>                                                  |
      | peopleCount       | int<0>                                                        |
#    And print last response
    Then the response status code should be 422
    And the enriched JSON nodes should be equal to:
      | hydra:title                | An error occurred                               |
      | violations[0].propertyPath | contactsCount                                   |
      | violations[0].message      | Die Anzahl direkter Kontakte muss gesetzt sein. |

    And I can find the following wayPoints in database:
      | locationName | contactsCount |
      | Ackis        | int<7>        |
    And there are exactly 2 wayPoints in database
