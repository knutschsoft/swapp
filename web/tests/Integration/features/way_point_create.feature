Feature: Testing wayPoint create resource

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
#    Given the following way points exists:
#      | locationName | walkName    |
#      | Assieck      | Spaziergang |
#      | BOTW         | Gamescon    |
#      | BOTW2        | Gamescon    |

  @api @wayPointCreate
  Scenario: I can request /api/way_points/create as authenticated user and will create wayPoint

    Given I am authenticated against api as "two@pac.de"
    When I send an api platform "POST" request to "/api/way_points/create" with parameters:
      | key               | value                                                         |
      | walk              | walkIri<Spaziergang>                                          |
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
      | locationName    | ageGroups                                          | imageName | wayPointTags  | contactsCount |
      | \| Google holla | 1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1 | <null>    | Gewalt,Drogen | <null>        |

    And there are exactly 1 wayPoints in database

  @api @wayPointCreate @imageFile
  Scenario: I can request /api/way_points/create as authenticated user and will create wayPoint with an imageFile

    Given I am authenticated against api as "two@pac.de"
    And I can not find the file "/images/way_points/timestamp<now>_AreYouDrunk.jpg" in public folder
    When I send an api platform "POST" request to "/api/way_points/create" with parameters:
      | key               | value                                                         |
      | walk              | walkIri<Spaziergang>                                          |
      | locationName      | \| <br><br><a href=“https:///www.google.com”>Google</a> holla |
      | note              | High and out.                                                 |
      | oneOnOneInterview | Sonne                                                         |
      | isMeeting         | <false>                                                       |
      | ageGroups         | ageGroups<1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1> |
      | wayPointTags      | tagIris<Gewalt,Drogen>                                        |
      | imageFileName     | AreYouDrunk.jpg                                               |
      | imageFileData     | @image.jpg                                                    |
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
      | userGroups        | userGroups<>                                                  |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type        | WayPoint        |
      | locationName | \| Google holla |
    And the JSON nodes should contain:
      | imageName | _AreYouDrunk.jpg                    |
      | imageSrc  | http://localhost/images/way_points/ |
      | imageSrc  | _AreYouDrunk.jpg                    |

    Given I can find the following wayPoints in database:
      | locationName    | ageGroups                                          | imageName                      | contactsCount |
      | \| Google holla | 1-2,m,7;1-2,w,3;1-2,x,1;3-10,m,7;3-10,w,3;3-10,x,1 | timestamp<now>_AreYouDrunk.jpg | <null>        |

    And I can find the file "/images/way_points/timestamp<now>_AreYouDrunk.jpg" in public folder
    And there are exactly 1 wayPoints in database

  @api @wayPointCreate
  Scenario: I can request /api/way_points/create as authenticated user with wrong client-walk-combination and will try to create a wayPoint

    Given I am authenticated against api as "karl@gamer.de"
    When I send an api platform "POST" request to "/api/way_points/create" with parameters:
      | key               | value                                                         |
      | walk              | walkIri<Spaziergang>                                          |
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
    And the JSON nodes should contain:
      | hydra:description | Item not found for |
#    And the JSON nodes should contain:
#      | hydra:description | /api/tags |
    And the JSON nodes should contain:
      | hydra:description | /api/walks |

  @api @wayPointCreate
  Scenario: I can request /api/way_points/create as authenticated user with wrong client-wayPointTags-combination and will try to create a wayPoint

    Given I am authenticated against api as "karl@gamer.de"
    When I send an api platform "POST" request to "/api/way_points/create" with parameters:
      | key               | value                                                         |
      | walk              | walkIri<Gamescon>                                             |
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
    And the JSON nodes should contain:
      | hydra:description | Item not found for |
    And the JSON nodes should contain:
      | hydra:description | /api/tags |
