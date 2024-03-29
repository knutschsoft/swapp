Feature: Testing wayPoint delete resource

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
      | locationName | walkName    | tags           |
      | Assieck      | Spaziergang | Drogen, Gewalt |
      | Spass        | Spaziergang | Drogen, Gewalt |

  @api @wayPoint @remove
  Scenario: I can request /api/way_points/remove as authenticated user and will delete a wayPoint including its tags
    Given I am authenticated against api as "admin@gmx.de"
    Given I can find the following wayPoints in database:
      | locationName | imageName | contactsCount |
      | Assieck      | <null>    | <null>        |
    And there are exactly 4 tagWayPoints in database
    And there are exactly 2 wayPoints in database
    When I send an api platform "POST" request to "/api/way_points/remove" with parameters:
      | key      | value                |
      | wayPoint | wayPointIri<Assieck> |
#    And print last response
    Then the response status code should be 200

    And I can not find the following wayPoints in database:
      | locationName |
      | Assieck      |
    And there are exactly 1 wayPoints in database
    And there are exactly 2 tagWayPoints in database

  @api @wayPoint @remove
  Scenario: I can request /api/way_points/remove as authenticated user and will not be able to delete a wayPoint when I am not an admin
    Given I am authenticated against api as "two@pac.de"
    And there are exactly 2 wayPoints in database
    Given I can find the following wayPoints in database:
      | locationName | imageName | contactsCount |
      | Assieck      | <null>    | <null>        |
    When I send an api platform "POST" request to "/api/way_points/remove" with parameters:
      | key      | value                |
      | wayPoint | wayPointIri<Assieck> |
#    And print last response
    Then the response status code should be 403
    And the JSON nodes should be equal to:
      | @type             | hydra:Error       |
      | hydra:title       | An error occurred |
      | hydra:description | Access Denied.    |

    And I can find the following wayPoints in database:
      | locationName |
      | Assieck      |
    And there are exactly 2 wayPoints in database

  @api @wayPoint @remove
  Scenario: I can request /api/way_points/remove as authenticated user and will not be able to delete a wayPoint of another client
    Given I am authenticated against api as "admin@gamer.de"
    And there are exactly 2 wayPoints in database
    Given I can find the following wayPoints in database:
      | locationName | imageName | contactsCount |
      | Assieck      | <null>    | <null>        |
    When I send an api platform "POST" request to "/api/way_points/remove" with parameters:
      | key      | value                |
      | wayPoint | wayPointIri<Assieck> |
#    And print last response
    Then the response status code should be 400
    And the JSON nodes should be equal to:
      | @type       | hydra:Error       |
      | hydra:title | An error occurred |
    And the JSON nodes should contain:
      | hydra:description | Item not found for "/api/way_points/ |

    And I can find the following wayPoints in database:
      | locationName |
      | Assieck      |
    And there are exactly 2 wayPoints in database

  @api @wayPoint @remove
  Scenario: I can request /api/way_points/remove as authenticated user and will delete a wayPoint with an image
    Given I am authenticated against api as "admin@gmx.de"
    Given I can find the following wayPoints in database:
      | locationName | imageName | contactsCount |
      | Assieck      | <null>    | <null>        |

    And there are exactly 3 clients in database
    And there are exactly 8 users in database
    And there are exactly 3 teams in database
    And there are exactly 2 systemicQuestions in database
    And there are exactly 3 tags in database
    And there are exactly 2 walks in database
    And there are exactly 2 wayPoints in database

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
      | visitedAt         | date<now,Y-m-dTH:i:s+02:00>                                   |
      | userGroups        | userGroups<>                                                  |
      | peopleCount       | int<0>                                                        |
    Then I can find the file "/images/way_points/timestamp<now>_AreYouDrunk.jpg" in public folder

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/way_points/remove" with parameters:
      | key      | value                |
      | wayPoint | wayPointIri<Assieck> |
#    And print last response
    Then the response status code should be 200

    And I can not find the following wayPoints in database:
      | locationName |
      | Assieck      |
    And I can not find the file "/images/way_points/timestamp<now>_AreYouDrunk.jpg" in public folder
    And there are exactly 3 clients in database
    And there are exactly 8 users in database
    And there are exactly 3 teams in database
    And there are exactly 2 systemicQuestions in database
    And there are exactly 3 tags in database
    And there are exactly 2 walks in database
    And there are exactly 1 wayPoints in database
