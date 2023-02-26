Feature: Testing walk export resource

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
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following teams exists:
      | name     | users                  | ageRanges          | client        | isWithContactsCount |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de | <false>             |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 | client@gmx.de | <false>             |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  | <true>              |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name        | team   | startTime  |
      | Spaziergang | CA     | 01.02.2021 |
      | Gamescon    | Gamers | 01.03.2021 |
    Given the following way points exists:
      | locationName | walkName    | contactsCount |
      | Assieck      | Spaziergang | <null>        |
      | BOTW         | Gamescon    | int<3>        |
      | BOTW2        | Gamescon    | int<2>        |

  @api @walkExport
  Scenario: I can request /api/walks/export as a not authenticated user and an auth error will occur
    When I add "content-type" header equal to "text/csv"
    When I send a GET request to "/api/walks/export"
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated admin with a client i do not belong to and will only my walks
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform GET request to "/api/walks/export" with parameters:
      | key | value |
#    And print last response
    And the response status code should be 200
    And the response should contain ",Spaziergang,"
    And the response should not contain "Gamescon"

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated user with date range given which will result in filled csv
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC&startTime[after]=2020-12-31T23:00:00.000Z&startTime[before]=2021-12-31T22:59:59.999Z" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,\"Beginn Wochentag\",Ende,\"Ende Wochentag\",Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"Anzahl Personen vor Ort\",\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,0,Arschkalt,0,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated user with contacts count which will result in filled csv with additional header
    Given I am authenticated against api as "karl@gamer.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC&startTime[after]=2020-12-31T23:00:00.000Z&startTime[before]=2021-12-31T22:59:59.999Z" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,\"Beginn Wochentag\",Ende,\"Ende Wochentag\",Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"Anzahl direkter Kontakte\",\"Anzahl Personen vor Ort\""
    And the response should not contain ",Spaziergang,"
    And the response should contain ",Gamescon,\"01.03.2021 00:00:00\",Montag,,,,1,\"How are you?\",,,,0,Arschkalt,0,\"My daily concept.\",Gamers,5,0"
    And the response should contain "Gamescon"
    And the response should not contain "BOTW"

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated user with date range given which will result in empty csv
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC&startTime[after]=2019-12-31T23:00:00.000Z&startTime[before]=2020-12-31T22:59:59.999Z" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should not contain "Id"
    And the response should not contain "Name"
    And the response should not contain "Beginn"
    And the response should not contain ",Spaziergang,"
    And the response should not contain "How are you?"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated user and will see all way points
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,\"Beginn Wochentag\",Ende,\"Ende Wochentag\",Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"Anzahl Personen vor Ort\",\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,0,Arschkalt,0,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

    Given I am authenticated against api as "lonely@gmx.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,\"Beginn Wochentag\",Ende,\"Ende Wochentag\",Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"Anzahl Personen vor Ort\",\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,0,Arschkalt,0,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,\"Beginn Wochentag\",Ende,\"Ende Wochentag\",Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"Anzahl Personen vor Ort\",\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,0,Arschkalt,0,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC" with parameters:
      | key | value |
    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,\"Beginn Wochentag\",Ende,\"Ende Wochentag\",Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname"
    And the response should contain "\"Anzahl direkter Kontakte\",\"Anzahl Personen vor Ort\",\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,0,Arschkalt,0,\"My daily concept.\",CA,,0,0,0,0,0,0,0,0,0,0"
    And the response should contain "Gamescon"
    And the response should not contain "BOTW"
