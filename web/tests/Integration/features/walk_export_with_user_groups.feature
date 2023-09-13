Feature: Testing walk export resource with UserGroups

  Background:
    Given the following clients exists:
      | email        |
      | gamer@gmx.de |
    Given the following users exists:
      | email         | client       |
      | karl@gamer.de | gamer@gmx.de |
    Given the following teams exists:
      | name   | users         | client       | userGroupNames                                                                                 | isWithUserGroups | ageRanges | isWithAgeRanges |
      | Gamers | karl@gamer.de | gamer@gmx.de | Aktuell Nutzende,jemals genutzt - nutzungsberechtigt,jemals genutzt - nicht nutzungsberechtigt | <true>           | 1-10      | <true>          |
    Given the following systemic questions exists:
      | question       | client       |
      | Esta muy bien? | gamer@gmx.de |
    Given the following tags exists:
      | name   | color     | client       |
      | Gewalt | Chocolate | gamer@gmx.de |
    Given the following walks exists:
      | name        | team   | startTime  |
      | Spaziergang | Gamers | 01.02.2021 |
      | Gamescon    | Gamers | 01.03.2021 |
    Given the following way points exists:
      | locationName | walkName    | userGroups                                                                                           | ageGroups                  |
      | Assieck      | Spaziergang | Aktuell Nutzende,2;jemals genutzt - nutzungsberechtigt,3;jemals genutzt - nicht nutzungsberechtigt,5 | 1-10,m,1;1-10,w,2;1-10,x,1 |
      | BOTW         | Gamescon    | Aktuell Nutzende,1;jemals genutzt - nutzungsberechtigt,1;jemals genutzt - nicht nutzungsberechtigt,4 | 1-10,m,1;1-10,w,4;1-10,x,1 |
      | BOTW2        | Gamescon    | Aktuell Nutzende,4;jemals genutzt - nutzungsberechtigt,1;jemals genutzt - nicht nutzungsberechtigt,3 | 1-10,m,1;1-10,w,9;1-10,x,1 |

  @api @walkExport @userGroups
  Scenario: I can request /api/walks/export as authenticated user with contacts count which will result in filled csv with additional header
    Given I am authenticated against api as "karl@gamer.de"
    When I send an api platform GET request to "/api/walks/export?order[walk.id]=DESC&startTime[after]=2020-12-31T23:00:00.000Z&startTime[before]=2021-12-31T22:59:59.999Z" with parameters:
      | key | value |
#    And print last response
    Then the response status code should be 200
    And the response should contain "Teilnehmende,Rundenersteller,\"Anzahl Personen vor Ort\",\"Aktuell Nutzende\",\"jemals genutzt - nutzungsberechtigt\",\"jemals genutzt - nicht nutzungsberechtigt\",\"angetroffene w 1-10\""
    And the response should contain "karl@gamer.de,,4,2,3,5,2"
    And the response should contain "karl@gamer.de,,17,5,2,7,13"
