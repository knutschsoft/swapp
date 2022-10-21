Feature: Testing wayPoint change resource

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
      | pinky@gamer.de    |                  | gamer@gmx.de  |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | admin@gamer.de    | ROLE_ADMIN       | gamer@gmx.de  |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following teams exists:
      | name     | users                        | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de       | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de                   | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de,pinky@gamer.de |                    | gamer@gmx.de  |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
      | How are you?   | gamer@gmx.de  |
    Given the following tags exists:
      | name        | color     | client        |
      | Gewalt      | Chocolate | client@gmx.de |
      | Drogen      | Blue      | client@gmx.de |
      | Kulleraugen | Blue      | gamer@gmx.de  |
    Given the following walks exists:
      | name        | team   | ageRanges |
      | Spaziergang | CA     | 1-2,3-10  |
      | Gamescon    | Gamers | 1-2,3-10  |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |
      | BOTW         | Gamescon    |
      | BOTW2        | Gamescon    |

  @api @wayPointChange @security
  Scenario: I can request /api/way_points/change as authenticated user and will change locationName of a wayPoint with script tags inside

    Given I can find the following wayPoints in database:
      | locationName | ageGroups                                          |
      | Assieck      | 1-2,m,0;1-2,w,0;1-2,x,0;3-10,m,0;3-10,w,0;3-10,x,0 |
    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Assieck>                                          |
      | locationName      | \| <br><br><a href=“https:///www.google.com”>Google</a> holla |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
      | userGroups        | userGroups<>                                                  |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type        | WayPoint        |
      | locationName | \| Google holla |

    Given I can find the following wayPoints in database:
      | locationName    | ageGroups                                          |
      | \| Google holla | 1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1 |

    And there are exactly 3 wayPoints in database

  @api @wayPointChange
  Scenario: I can request /api/way_points/change as authenticated user with wrong client and will try to change of a wayPoint

    Given I can find the following wayPoints in database:
      | locationName | ageGroups                                          |
      | Assieck      | 1-2,m,0;1-2,w,0;1-2,x,0;3-10,m,0;3-10,w,0;3-10,x,0 |
    Given I am authenticated against api as "karl@gamer.de"
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Assieck>                                          |
      | locationName      | \| <br><br><a href=“https:///www.google.com”>Google</a> holla |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
#    And print last response
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |

  @api @wayPointChange
  Scenario: I can request /api/way_points/change as authenticated user and will change tags of a wayPoint of which i do not have access to

    Given I can find the following wayPoints in database:
      | locationName | ageGroups                                          |
      | Assieck      | 1-2,m,0;1-2,w,0;1-2,x,0;3-10,m,0;3-10,w,0;3-10,x,0 |
    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Assieck>                                          |
      | locationName      | \| <br><br><a href=“https:///www.google.com”>Google</a> holla |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Kulleraugen>                                          |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
#    And print last response
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |
