Feature: Testing walk change-start-time resource

  Background:
    Given the following clients exists:
      | email         |
      | gamer@gmx.de  |
      | client@gmx.de |
    Given the following users exists:
      | email          | roles      | client        |
      | karl@gmx.de    |            | client@gmx.de |
      | karl@gamer.de  |            | gamer@gmx.de  |
      | admin@gamer.de | ROLE_ADMIN | gamer@gmx.de  |
    Given the following teams exists:
      | name   | users         | ageRanges | client       |
      | Gamers | karl@gamer.de |           | gamer@gmx.de |
    Given the following walks exists:
      | name     | team   | startTime                                   |
      | Gamescon | Gamers | date<2020-06-12T15:51:06+00:00,d.m.Y H:i:s> |

  @api @walkChangeStartTime
  Scenario: I can request /api/walks/change-start-time as authenticated admin and will change a walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/change-start-time" with parameters:
      | key       | value                     |
      | walk      | walkIri<Gamescon>         |
      | startTime | 2021-05-11T15:51:06+00:00 |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type     | Walk                      |
      | name      | Gamescon                  |
      | startTime | 2021-05-11T15:51:06+00:00 |

    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name     | startTime                                   |
      | Gamescon | date<2021-05-11T15:51:06+00:00,d.m.Y H:i:s> |

  @api @walkChangeStartTime
  Scenario: I can request /api/walks/change-start-time as authenticated user and will change a walk
    Given I am authenticated against api as "admin@gamer.de"
    When I send an api platform "POST" request to "/api/walks/change-start-time" with parameters:
      | key       | value                     |
      | walk      | walkIri<Gamescon>         |
      | startTime | 2021-05-11T15:51:06+00:00 |
#    And print last response
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | @type     | Walk                      |
      | name      | Gamescon                  |
      | startTime | 2021-05-11T15:51:06+00:00 |

    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name     | startTime                                   |
      | Gamescon | date<2021-05-11T15:51:06+00:00,d.m.Y H:i:s> |

  @api @walkChangeStartTime
  Scenario: I can not request /api/walks/change-start-time as unauthenticated user
    When I send an api platform "POST" request to "/api/walks/change-start-time" with parameters:
      | key       | value                     |
      | walk      | walkIri<Gamescon>         |
      | startTime | 2021-05-11T15:51:06+00:00 |
#    And print last response
    Then the response status code should be 401
    And the JSON nodes should be equal to:
      | message | JWT Token not found |

    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name     | startTime                                   |
      | Gamescon | date<2020-06-12T15:51:06+00:00,d.m.Y H:i:s> |

  @api @walkChangeStartTime
  Scenario: I can not request /api/walks/change-start-time as user with wrong client
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform "POST" request to "/api/walks/change-start-time" with parameters:
      | key       | value                     |
      | walk      | walkIri<Gamescon>         |
      | startTime | 2021-05-11T15:51:06+00:00 |
#    And print last response
    Then the response status code should be 400
    And the JSON node "hydra:description" should match "/Item not found for *./"

    And there are exactly 1 walks in database
    And I can find the following walks in database:
      | name     | startTime                                   |
      | Gamescon | date<2020-06-12T15:51:06+00:00,d.m.Y H:i:s> |
