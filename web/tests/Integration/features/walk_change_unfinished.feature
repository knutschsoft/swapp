Feature: Testing walk change unfinished resource

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

  @api @walkChangeUnfinished
  Scenario: I can request /api/walks/change-unfinished as a not authenticated user and an auth error will occur
    When I send an api platform "POST" request to "/api/walks/change-unfinished" with parameters:
      | key  | value             |
      | team | teamIri<Westhang> |
    Then the response status code should be 401
#    And print last response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @walkChangeUnfinished
  Scenario: I can request /api/walks/change-unfinished as a normal user and an access denied error will occur
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/change-unfinished" with parameters:
      | key  | value                |
      | walk | walkIri<Spaziergang> |
#    And print last JSON response
    Then the response status code should be 403
    And the JSON nodes should be equal to:
      | hydra:description | Access Denied. |

  @api @walkChangeUnfinished
  Scenario: I can not request /api/walks/change-unfinished for a walk as an admin of another client
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform "POST" request to "/api/walks/change-unfinished" with parameters:
      | key  | value             |
      | walk | walkIri<Gamescon> |
    Then the response status code should be 400
    And the JSON nodes should contain:
      | hydra:description | Item not found for "/api/walks/ |

  @api @walkChangeUnfinished
  Scenario: I can request /api/walks/change-unfinished as authenticated user and will try to change a walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/change-unfinished" with parameters:
      | key  | value             |
      | walk | walkIri<Gamescon> |
      | team | teamIri<Westhang> |
#    And print last JSON response
    Then the response status code should be 422
    And the JSON nodes should be equal to:
      | violations[0].propertyPath | name                                |
      | violations[0].message      | Dieser Wert sollte nicht leer sein. |
      | violations[1].propertyPath | name                                |
      | violations[1].message      | Dieser Wert sollte nicht null sein. |
      | violations[2].propertyPath | conceptOfDay                        |
      | violations[2].message      | Dieser Wert sollte nicht null sein. |
      | violations[3].propertyPath | weather                             |
      | violations[3].message      | Dieser Wert sollte nicht null sein. |
      | violations[4].propertyPath | startTime                           |
      | violations[4].message      | Dieser Wert sollte nicht null sein. |
      | violations[5].propertyPath | holidays                            |
      | violations[5].message      | Dieser Wert sollte nicht null sein. |
      | violations[6].propertyPath | walkTeamMembers                     |
      | violations[6].message      | Dieser Wert sollte nicht null sein. |
      | violations[7].propertyPath | guestNames                          |
      | violations[7].message      | Dieser Wert sollte nicht null sein. |
