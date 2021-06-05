Feature: Testing change password resource

  Background:
    Given the following users exists:
      | email          | confirmationToken                | isEnabled |
      | karl@gmx.de    | 01234567890123456789012345678000 | 1         |
      | lonely@gmx.de  | 01234567890123456789012345678444 | 0         |
      | empty@token.de | 01234567890123456789012345678912 | 1         |

  @api @changePassword
  Scenario: I can request /api/users/change-password with an invalid token and get a violation
    When I send an api platform "POST" request to "/api/users/change-password" with parameters:
      | key               | value                                               |
      | user              | userIri<karl@gmx.de>                                |
      | confirmationToken | confirmationToken<01234567890123456789012345678999> |
      | password          | narfgogogadgetzorp                                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred             |
      | violations[0].propertyPath | confirmationTokenValid        |
      | violations[0].message      | Dieser Wert sollte true sein. |

  @api @changePassword
  Scenario: I can request /api/users/change-password with a valid token and get the user back
    When I send an api platform "POST" request to "/api/users/change-password" with parameters:
      | key               | value                                               |
      | user              | userIri<karl@gmx.de>                                |
      | confirmationToken | confirmationToken<01234567890123456789012345678000> |
      | password          | narfgogogadgetzorp                                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the JSON nodes should be equal to:
      | @type | User        |
      | email | karl@gmx.de |
    And there is an empty confirmationToken for "karl@gmx.de"

  @api @changePassword
  Scenario: I can request /api/users/change-password with a valid token but a too short password
    When I send an api platform "POST" request to "/api/users/change-password" with parameters:
      | key               | value                                               |
      | user              | userIri<karl@gmx.de>                                |
      | confirmationToken | confirmationToken<01234567890123456789012345678000> |
      | password          | 123456                                              |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred                                                      |
      | violations[0].propertyPath | password                                                               |
      | violations[0].message      | Diese Zeichenkette ist zu kurz. Sie sollte mindestens 7 Zeichen haben. |
    And there is a non empty confirmationToken for "karl@gmx.de"

  @api @changePassword
  Scenario: I can request /api/users/change-password with a valid token but a too long password
    When I send an api platform "POST" request to "/api/users/change-password" with parameters:
      | key               | value                                               |
      | user              | userIri<karl@gmx.de>                                |
      | confirmationToken | confirmationToken<01234567890123456789012345678000> |
      | password          | 01234567890123456789012345678901234567890           |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred                                                      |
      | violations[0].propertyPath | password                                                               |
      | violations[0].message      | Diese Zeichenkette ist zu lang. Sie sollte höchstens 40 Zeichen haben. |
    And there is a non empty confirmationToken for "karl@gmx.de"

  @api @changePassword
  Scenario: I can request /api/users/change-password with an valid empty token but will get a violation
    When there is an empty confirmationToken for "empty@token.de"
    When I send an api platform "POST" request to "/api/users/change-password" with parameters:
      | key               | value                                               |
      | user              | userIri<empty@token.de>                             |
      | confirmationToken | confirmationToken<01234567890123456789012345678912> |
      | password          | narfgogogadgetzorp                                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred             |
      | violations[0].propertyPath | confirmationTokenValid        |
      | violations[0].message      | Dieser Wert sollte true sein. |
    And there is an empty confirmationToken for "empty@token.de"

  @api @changePassword
  Scenario: I can request /api/users/change-password with an valid empty token of disabled user
    When I send an api platform "POST" request to "/api/users/change-password" with parameters:
      | key               | value                                               |
      | user              | userIri<lonely@gmx.de>                              |
      | confirmationToken | confirmationToken<01234567890123456789012345678444> |
      | password          | narfgogogadgetzorp                                  |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the JSON nodes should be equal to:
      | @type | User          |
      | email | lonely@gmx.de |
    And there is an empty confirmationToken for "lonely@gmx.de"
