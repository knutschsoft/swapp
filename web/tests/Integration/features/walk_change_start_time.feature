Feature: Testing walk change resource

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
      | admin@gamer.de    | ROLE_ADMIN       | gamer@gmx.de  |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following teams exists:
      | name     | users                  | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de             | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |
    Given the following systemic questions exists:
      | question       | client        |
      | Esta muy bien? | client@gmx.de |
      | How are you?   | gamer@gmx.de  |
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

  @api @walkChange
  Scenario: I can request /api/walks/change as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/walks/change" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkChange
  Scenario: I can not request /api/walks/change for a walk as an admin of another client
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/change" with parameters:
      | key  | value             |
      | walk | walkIri<Gamescon> |
    Then the response status code should be 400
    And the JSON nodes should contain:
      | hydra:description | Item not found for "/api/walks/ |


  @api @walkChange
  Scenario: I can request /api/walks/change as authenticated user and will change a new walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/change" with parameters:
      | key  | value             |
      | walk | walkIri<Gamescon> |
      | team | teamIri<Westhang> |
#    And print last response
    Then the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath  | name                                |
      | violations[0].message       | Dieser Wert sollte nicht leer sein. |
      | violations[1].propertyPath  | name                                |
      | violations[1].message       | Dieser Wert sollte nicht null sein. |
      | violations[2].propertyPath  | commitments                         |
      | violations[2].message       | Dieser Wert sollte nicht null sein. |
      | violations[3].propertyPath  | conceptOfDay                        |
      | violations[3].message       | Dieser Wert sollte nicht null sein. |
      | violations[4].propertyPath  | insights                            |
      | violations[4].message       | Dieser Wert sollte nicht null sein. |
      | violations[5].propertyPath  | systemicAnswer                      |
      | violations[5].message       | Dieser Wert sollte nicht null sein. |
      | violations[6].propertyPath  | walkReflection                      |
      | violations[6].message       | Dieser Wert sollte nicht null sein. |
      | violations[7].propertyPath  | rating                              |
      | violations[7].message       | Dieser Wert sollte nicht null sein. |
      | violations[8].propertyPath  | weather                             |
      | violations[8].message       | Dieser Wert sollte nicht null sein. |
      | violations[9].propertyPath  | startTime                           |
      | violations[9].message       | Dieser Wert sollte nicht null sein. |
      | violations[10].propertyPath | endTime                             |
      | violations[10].message      | Dieser Wert sollte nicht null sein. |
      | violations[11].propertyPath | holidays                            |
      | violations[11].message      | Dieser Wert sollte nicht null sein. |
      | violations[12].propertyPath | isResubmission                      |
      | violations[12].message      | Dieser Wert sollte nicht null sein. |
#      | violations[12].propertyPath | walkTeamMembers                     |
#      | violations[12].message      | Dieser Wert sollte nicht null sein. |

  @api @walkChange
  Scenario: I can request /api/walks/change as authenticated user and will change a new walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/change" with parameters:
      | key             | value                     |
      | walk            | walkIri<Gamescon>         |
      | name            | This is my Walk           |
      | conceptOfDay    | High and out.             |
      | weather         | Sonne                     |
      | walkTeamMembers | userIris<karl@gmx.de>     |
      | isResubmission  | <false>                   |
      | holidays        | <false>                   |
      | commitments     | narf                      |
      | insights        | zorp                      |
      | systemicAnswer  | zorp                      |
      | walkReflection  | zorp                      |
      | rating          | int<2>                    |
      | startTime       | 2021-05-11T15:51:06+00:00 |
      | endTime         | 2021-05-11T15:51:08+00:00 |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type            | Walk                      |
      | name             | This is my Walk           |
      | systemicQuestion | How are you?              |
      | weather          | Sonne                     |
      | startTime        | 2021-05-11T15:51:06+00:00 |
      | endTime          | 2021-05-11T15:51:08+00:00 |

    And there are exactly 2 walks in database
