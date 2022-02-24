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

  @api @wayPointChange
  Scenario: I can request /api/way_points/change as authenticated user and will change wayPoint
    Given I am authenticated against api as "two@pac.de"
    Given I can find the following wayPoints in database:
      | locationName | imageName |
      | Assieck      | <null>    |
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Assieck>                                          |
      | locationName      | Assieck                                                       |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | imageFileName     | AreYouDrunk.jpg                                               |
      | imageFileData     | @image.jpg                                                    |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type        | WayPoint |
      | locationName | Assieck  |
    And the JSON nodes should contain:
      | imageName | _AreYouDrunk.jpg                    |
      | imageSrc  | http://localhost/images/way_points/ |
      | imageSrc  | _AreYouDrunk.jpg                    |

    And I can find the following wayPoints in database:
      | locationName | ageGroups                                          | imageName                      | wayPointTags  |
      | Assieck      | 1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1 | timestamp<now>_AreYouDrunk.jpg | Gewalt,Drogen |
    And there are exactly 1 wayPoints in database
    And I can not find the file "/images/way_points/timestamp<now>_AreYouDrunkMyDear.jpg" in public folder
    And I can find the file "/images/way_points/timestamp<now>_AreYouDrunk.jpg" in public folder

    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/way_points/change" with parameters:
      | key               | value                                                         |
      | wayPoint          | wayPointIri<Assieck>                                          |
      | locationName      | Assieck                                                       |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | imageFileName     | AreYouDrunkMyDear.jpg                                         |
      | imageFileData     | @image.jpg                                                    |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type        | WayPoint |
      | locationName | Assieck  |
    And the JSON nodes should contain:
      | imageName | _AreYouDrunkMyDear.jpg              |
      | imageSrc  | http://localhost/images/way_points/ |
      | imageSrc  | _AreYouDrunkMyDear.jpg              |

    And I can find the following wayPoints in database:
      | locationName | ageGroups                                          | imageName                            |
      | Assieck      | 1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1 | timestamp<now>_AreYouDrunkMyDear.jpg |
    And I can find the file "/images/way_points/timestamp<now>_AreYouDrunkMyDear.jpg" in public folder
    And I can not find the file "/images/way_points/timestamp<now>_AreYouDrunk.jpg" in public folder
    And there are exactly 1 wayPoints in database
