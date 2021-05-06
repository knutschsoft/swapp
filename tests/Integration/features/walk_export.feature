Feature: Testing walk export resource

  Background:
    Given the following users exists:
      | email             | roles            |
      | karl@gmx.de       |                  |
      | lonely@gmx.de     |                  |
      | two@pac.de        |                  |
      | admin@gmx.de      | ROLE_ADMIN       |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN |
    Given the following teams exists:
      | name     | users                  | ageRanges          |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 |
    Given the following systemic questions exists:
      | question       |
      | Esta muy bien? |
    Given the following tags exists:
      | name   | color |
      | Gewalt | Green |
      | Drogen | Blue  |
    Given the following walks exists:
      | name        | team |
      | Spaziergang | CA   |
    Given the following way points exists:
      | locationName | walkName    |
      | Assieck      | Spaziergang |

  @api @walkExport
  Scenario: I can request /api/walks/export as a not authenticated user and an auth error will occur
    When I add "content-type" header equal to "text/csv"
    When I send a POST request to "/api/walks/export"
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkExport
  Scenario: I can request /api/walks/export as authenticated user and will see all way points
    Given I am authenticated against api as "karl@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send a POST request to "/api/walks/export"
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,,,,CA,0,0,0,0,0,0,0,0,0"

    Given I am authenticated against api as "lonely@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send a POST request to "/api/walks/export"
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,,,,CA,0,0,0,0,0,0,0,0,0"

    Given I am authenticated against api as "admin@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send a POST request to "/api/walks/export"
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,,,,CA,0,0,0,0,0,0,0,0,0"

    Given I am authenticated against api as "superadmin@gmx.de"
    When I add "content-type" header equal to "text/csv"
    When I send a POST request to "/api/walks/export"
#    And print last response
    Then the response status code should be 200
    And the response should contain "Id,Name,Beginn,Ende,Reflexion,Bewertung,\"systemische Frage\",\"systemische Antwort\",\"Erkenntnisse, Überlegungen, Zielsetzungen\",\"Termine, Besorgungen, Verabredungen\",\"Wiedervorlage Dienstberatung\",Wetter,Ferien,Tageskonzept,Teamname,\"angetroffene w 1-10\",\"angetroffene m 1-10\",\"angetroffene d 1-10\",\"angetroffene w 3-12\",\"angetroffene m 3-12\",\"angetroffene d 3-12\",\"angetroffene w 13-90\",\"angetroffene m 13-90\",\"angetroffene d 13-90\""
    And the response should contain ",Spaziergang,"
    And the response should contain ",,1,\"How are you?\",,,,,,,,CA,0,0,0,0,0,0,0,0,0"
