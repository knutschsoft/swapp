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
      | name     | users                  | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name        | team   |
      | Spaziergang | CA     |
      | Gamescon    | Gamers |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |
      | BOTW         | Gamescon    |
      | BOTW2        | Gamescon    |

  @api @walkExport
  Scenario: I can request /api/walks/export as a not authenticated user and an auth error will occur
    When I add "content-type" header equal to "text/csv"
    When I send a POST request to "/api/walks/export"
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated admin with a client i do not belong to and will get access denied
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform POST request to "/api/walks/export" with parameters:
      | key    | value                   |
      | client | clientIri<gamer@gmx.de> |
#    And print last response
    Then the response should be in JSON
    And the response status code should be 400
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated user and will see all way points
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform POST request to "/api/walks/export" with parameters:
      | key    | value                    |
      | client | clientIri<client@gmx.de> |
    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,Arschkalt,,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

    Given I am authenticated against api as "lonely@gmx.de"
    When I send an api platform POST request to "/api/walks/export" with parameters:
      | key    | value                    |
      | client | clientIri<client@gmx.de> |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,Arschkalt,,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform POST request to "/api/walks/export" with parameters:
      | key    | value                    |
      | client | clientIri<client@gmx.de> |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,Arschkalt,,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"

    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform POST request to "/api/walks/export" with parameters:
      | key    | value                    |
      | client | clientIri<client@gmx.de> |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,Arschkalt,,\"My daily concept.\",CA,0,0,0,0,0,0,0,0,0"
    And the response should not contain "Gamescon"
    And the response should not contain "BOTW"
