Feature: Testing wayPoint csv export resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | others@gmx.de |
    Given the following users exists:
      | email         | roles | client        |
      | karl@gmx.de   |       | client@gmx.de |
      | others@gmx.de |       | others@gmx.de |
    Given the following teams exists:
      | name     | users         | client        | isWithUserGroups | userGroups     | ageRanges          |
      | Westhang | karl@gmx.de   | client@gmx.de | <false>          |                | 1-10,3-12, 13 - 90 |
      | CA       | karl@gmx.de   | client@gmx.de | <true>           | Nutzende,Dudes | 2-3                |
      | Others   | others@gmx.de | others@gmx.de | <true>           | Nutzende,Dudes | 7-11               |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
      | Esta muy bien? | others@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
      | Sex    | Navy      | client@gmx.de |
    Given the following walks exists:
      | name        | team     |
      | Spaziergang | Westhang |
      | Gamescon    | CA       |
      | Others      | Others   |
    Given the following way points exists:
      | locationName | walkName    | userGroups          | visitedAt        | ageGroups                  | tags   |
      | Assieck      | Spaziergang |                     | 20.12.2021 07:22 |                            | Gewalt |
      | Ackis        | Gamescon    | Nutzende,7;Dudes,2  | 20.12.2021 12:11 |                            | Drogen |
      | Others       | Others      | Nutzende,12;Dudes,1 | 20.12.2021 22:22 | 7-11,m,7;7-11,w,3;7-11,x,1 | Sex    |

  @api @wayPointExport
  Scenario: I can request /api/walks/export as a not authenticated user and an auth error will occur
    When I add "content-type" header equal to "text/csv"
    When I send a GET request to "/api/way_points/export"
#    And print last response
    Then the response status code should be 401
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @wayPointExport
  Scenario: I can request /api/way_points/export with itemsPerPage set to 1 and will get only one wayPoint
    Given I am authenticated against api as "karl@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send an api platform "GET" request to "/api/way_points/export?itemsPerPage=1&page=1&visitedAt[before]=date<20.12.2021 23:59:59,Y-m-d H:m:i>&visitedAt[after]=date<20.12.2021 00:00:00,Y-m-d H:m:i>" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "id,Ort,Ankunft,Wochentag,Rundenname,Teamname,Teilnehmende,Tageskonzept,Beobachtung,Einzelgespräch,Meeting?,\"direkte Kontakte\",\"Anzahl Personen vor Ort\""
    And the response should contain "Nutzende,Dudes,\"angetroffene w 2-3\",\"angetroffene m 2-3\",\"angetroffene d 2-3\""
    And the response should contain "Ackis,\"20.12.2021 12:11:00\",Montag,Gamescon,CA,karl@gmx.de,\"My daily concept.\",null,null,0,,0,7,2,0,0,0"
    And the response should not contain "angetroffene d 7-11"

  @api @wayPointExport
  Scenario: I can request /api/way_points/export and will get way_points of my client
    Given I am authenticated against api as "karl@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send an api platform "GET" request to "/api/way_points/export?itemsPerPage=1000&page=1&visitedAt[before]=date<20.12.2021 23:59:59,Y-m-d H:m:i>&visitedAt[after]=date<20.12.2021 00:00:00,Y-m-d H:m:i>" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "id,Ort,Ankunft,Wochentag,Rundenname,Teamname,Teilnehmende,Tageskonzept,Beobachtung,Einzelgespräch,Meeting?,\"direkte Kontakte\",\"Anzahl Personen vor Ort\""
    And the response should contain "Nutzende,Dudes,\"angetroffene w 2-3\",\"angetroffene m 2-3\",\"angetroffene d 2-3\""
    And the response should contain "Ackis,\"20.12.2021 12:11:00\",Montag,Gamescon,CA,karl@gmx.de,\"My daily concept.\",null,null,0,,0,,,,,,,,,,7,2,0,0,0,Drogen"
    And the response should contain "Spaziergang,Westhang,karl@gmx.de,\"My daily concept.\",null,null,0,,0,0,0,0,0,0,0,0,0,0,,,,,,Gewalt"
    And the response should contain "angetroffene d 3-12"
    And the response should not contain "angetroffene d 7-11"

  @api @wayPointExport
  Scenario: I can request /api/way_points/export and will get way_points with ageRanges
    Given I am authenticated against api as "others@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send an api platform "GET" request to "/api/way_points/export?itemsPerPage=1000&page=1&visitedAt[before]=date<20.12.2021 23:59:59,Y-m-d H:m:i>&visitedAt[after]=date<20.12.2021 00:00:00,Y-m-d H:m:i>" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Ort,Ankunft,Wochentag,Rundenname,Teamname,Teilnehmende,Tageskonzept,Beobachtung,Einzelgespräch,Meeting?,\"direkte Kontakte\",\"Anzahl Personen vor Ort\",Nutzende,Dudes,\"angetroffene w 7-11\",\"angetroffene m 7-11\",\"angetroffene d 7-11\""
    And the response should contain "Others,\"20.12.2021 22:22:00\",Montag,Others,Others,others@gmx.de,\"My daily concept.\",null,null,0,,11,12,1,3,7,1"
    And the response should not contain "angetroffene d 3-12"
    And the response should not contain "Ackis"
    And the response should not contain "Assieck"

  @api @wayPointExport
  Scenario: I can request /api/way_points/export and will get way_points with tags with one tag for each column
    Given I am authenticated against api as "others@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send an api platform "GET" request to "/api/way_points/export?itemsPerPage=1000&page=1&visitedAt[before]=date<20.12.2021 23:59:59,Y-m-d H:m:i>&visitedAt[after]=date<20.12.2021 00:00:00,Y-m-d H:m:i>" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Ort,Ankunft,Wochentag,Rundenname,Teamname,Teilnehmende,Tageskonzept,Beobachtung,Einzelgespräch,Meeting?,\"direkte Kontakte\",\"Anzahl Personen vor Ort\",Nutzende,Dudes,\"angetroffene w 7-11\",\"angetroffene m 7-11\",\"angetroffene d 7-11\",Tags"
    And the response should contain "Others,\"20.12.2021 22:22:00\",Montag,Others,Others,others@gmx.de,\"My daily concept.\",null,null,0,,11,12,1,3,7,1,Sex"
    And the response should not contain "angetroffene d 3-12"
    And the response should not contain "Ackis"
    And the response should not contain "Assieck"
