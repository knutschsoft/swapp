Feature: Testing user resource with walk filter timeRange

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
      | dr@dre.de         |                  | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
    Given the following teams exists:
      | name     | users                  | ageRanges          | client        |
      | Westhang | karl@gmx.de,two@pac.de | 1-10,3-12, 13 - 90 | client@gmx.de |
      | CA       | two@pac.de,dr@dre.de   | 1-10,3-12, 13 - 90 | client@gmx.de |
      | Gamers   | karl@gamer.de          |                    | gamer@gmx.de  |
    Given the following walks exists:
      | name        | team   | startTime |
      | Gassi       | CA     | 4.09.2021 |
      | Gassi2      | CA     | 4.11.2021 |
      | Spaziergang | CA     | 5.10.2021 |
      | Gamescon    | Gamers | 5.10.2021 |

  @api @user
  Scenario: I can request /api/users as an user and get all users which have done a walk in a specific time
    Given I am authenticated against api as "karl@gamer.de"
    When I send a GET request to "/api/users?walks.timeRange=2021-09-30T22:00:00.000Z..2021-10-31T22:59:59.999Z"
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems         | 1             |
      | hydra:member[0].username | karl@gamer.de |

    Given I am authenticated against api as "dr@dre.de"
    When I send a GET request to "/api/users?walks.timeRange=2021-09-30T22:00:00.000Z..2021-10-31T22:59:59.999Z"
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems         | 2          |
      | hydra:member[0].username | two@pac.de |
      | hydra:member[1].username | dr@dre.de  |

  @api @user
  Scenario: I can request /api/users as an admin user and get all users which have done a walk in a specific time
    Given I am authenticated against api as "admin@gmx.de"
    When I send a GET request to "/api/users?walks.timeRange=2021-09-30T22:00:00.000Z..2021-10-31T22:59:59.999Z"
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems         | 2          |
      | hydra:member[0].username | two@pac.de |
      | hydra:member[1].username | dr@dre.de  |

  @api @user
  Scenario: I can request /api/users as a superadmin and get all users of all clients which have done a walk in a specific time
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send a GET request to "/api/users?walks.timeRange=2021-09-30T22:00:00.000Z..2021-10-31T22:59:59.999Z"
    Then the response should be in JSON
    And the response status code should be 200
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:totalItems         | 3             |
      | hydra:member[0].username | two@pac.de    |
      | hydra:member[1].username | dr@dre.de     |
      | hydra:member[2].username | karl@gamer.de |
