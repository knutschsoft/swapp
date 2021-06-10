Feature: Testing is confirmation token valid resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email          | confirmationToken                | isEnabled | client        |
      | karl@gmx.de    | 01234567890123456789012345678000 | 1         | client@gmx.de |
      | lonely@gmx.de  | 01234567890123456789012345678444 | 0         | client@gmx.de |
      | empty@token.de | 01234567890123456789012345678912 | 1         | client@gmx.de |

  @api @isConfirmationTokenValid
  Scenario: I can request /api/users/is-confirmation-token-valid with an invalid token
    When I send an api platform "POST" request to "/api/users/is-confirmation-token-valid" with parameters:
      | key               | value                                               |
      | user              | userIri<karl@gmx.de>                                |
      | confirmationToken | confirmationToken<01234567890123456789012345678999> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred             |
      | violations[0].propertyPath | confirmationTokenValid        |
      | violations[0].message      | Dieser Wert sollte true sein. |

  @api @isConfirmationTokenValid
  Scenario: I can request /api/users/is-confirmation-token-valid with an valid token and get the user back
    When I send an api platform "POST" request to "/api/users/is-confirmation-token-valid" with parameters:
      | key               | value                                               |
      | user              | userIri<karl@gmx.de>                                |
      | confirmationToken | confirmationToken<01234567890123456789012345678000> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the JSON nodes should be equal to:
      | @type | User        |
      | email | karl@gmx.de |

  @api @isConfirmationTokenValid
  Scenario: I can request /api/users/is-confirmation-token-valid with an valid empty token but will get a violation
    When I send an api platform "POST" request to "/api/users/is-confirmation-token-valid" with parameters:
      | key               | value                                               |
      | user              | userIri<empty@token.de>                             |
      | confirmationToken | confirmationToken<01234567890123456789012345678912> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred             |
      | violations[0].propertyPath | confirmationTokenValid        |
      | violations[0].message      | Dieser Wert sollte true sein. |

  @api @isConfirmationTokenValid
  Scenario: I can request /api/users/is-confirmation-token-valid with a disabled user
    When I send an api platform "POST" request to "/api/users/is-confirmation-token-valid" with parameters:
      | key               | value                                               |
      | user              | userIri<lonely@gmx.de>                              |
      | confirmationToken | confirmationToken<01234567890123456789012345678444> |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 200
    And the JSON nodes should be equal to:
      | @type | User          |
      | email | lonely@gmx.de |
