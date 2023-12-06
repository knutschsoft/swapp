Feature: Testing walk epilogue resource

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
      | name     | users                        | ageRanges          | client        | isWithSystemicQuestion |
      | Westhang | karl@gmx.de,two@pac.de       | 1-10,3-12, 13 - 90 | client@gmx.de | <true>                 |
      | CA       | two@pac.de                   | 1-10,3-12, 13 - 90 | client@gmx.de | <true>                 |
      | Gamers   | karl@gamer.de,pinky@gamer.de |                    | gamer@gmx.de  | <true>                 |
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

  @api @walkEpilogue @security
  Scenario: I can request /api/walks/epilogue as authenticated user and will change walk name of a walk with script tags inside

    Given I can find the following walks in database:
      | name     | walkTeamMembers              |
      | Gamescon | karl@gamer.de,pinky@gamer.de |
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/epilogue" with parameters:
      | key            | value                                                         |
      | walk           | walkIri<Gamescon>                                             |
      | name           | \| <br><br><a href=“https:///www.google.com”>Google</a> holla |
      | conceptOfDay   | array<High and out.>                                          |
      | weather        | Sonne                                                         |
      | isResubmission | <false>                                                       |
      | holidays       | <false>                                                       |
      | commitments    | narf                                                          |
      | insights       | zorp                                                          |
      | systemicAnswer | zorp                                                          |
      | walkReflection | zorp                                                          |
      | rating         | int<2>                                                        |
      | startTime      | 2021-05-11T15:51:06+00:00                                     |
      | endTime        | 2030-05-11T15:51:08+00:00                                     |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type | Walk            |
      | name  | \| Google holla |

    Given I can find the following walks in database:
      | name            | rating |
      | \| Google holla | int<2> |

    And there are exactly 2 walks in database


  @api @walkChange
  Scenario: I can request /api/walks/epilogue as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/walks/epilogue" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkEpilogue
  Scenario: I can not request /api/walks/epilogue for a walk as an admin of another client
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/epilogue" with parameters:
      | key  | value             |
      | walk | walkIri<Gamescon> |
    Then the response status code should be 400
    And the JSON nodes should contain:
      | hydra:description | Item not found for "/api/walks/ |


  @api @walkEpilogue
  Scenario: I can request /api/walks/epilogue as authenticated user and will try to epilogue a walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/epilogue" with parameters:
      | key  | value             |
      | walk | walkIri<Gamescon> |
#    And print last JSON response
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
    And the JSON nodes should be equal to:
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

  @api @walkEpilogue
  Scenario: I can request /api/walks/epilogue as authenticated user and will epilogue a walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/epilogue" with parameters:
      | key            | value                     |
      | walk           | walkIri<Gamescon>         |
      | name           | This is my Walk           |
      | conceptOfDay   | array<High and out.>      |
      | weather        | Sonne                     |
      | isResubmission | <false>                   |
      | holidays       | <false>                   |
      | commitments    | narf                      |
      | insights       | zorp                      |
      | systemicAnswer | zorp                      |
      | walkReflection | zorp                      |
      | rating         | int<2>                    |
      | startTime      | 2021-05-11T15:51:06+00:00 |
      | endTime        | 2030-05-11T15:51:08+00:00 |
#    And print last response
    Then the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type            | Walk                      |
      | name             | This is my Walk           |
      | systemicQuestion | How are you?              |
      | weather          | Sonne                     |
      | startTime        | 2021-05-11T15:51:06+00:00 |
      | endTime          | 2030-05-11T15:51:08+00:00 |

    And there are exactly 2 walks in database
