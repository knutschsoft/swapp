Feature: Testing request password reset resource

  Background:
    Given the following users exists:
      | email         | isEnabled |
      | karl@gmx.de   | 1         |
      | lonely@gmx.de | 0         |

  @api @requestPasswordReset
  Scenario: I can request /api/users/request-password-reset with an invalid user and get a violation
    When I send an api platform "POST" request to "/api/users/request-password-reset" with parameters:
      | key      | value         |
      | username | mykarl@gmx.de |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred                                                         |
      | violations[0].propertyPath | username                                                                  |
      | violations[0].message      | Der Nutzer "mykarl@gmx.de" existiert nicht.                               |
      | violations[1].propertyPath | username                                                                  |
      | violations[1].message      | Der Nutzer "mykarl@gmx.de" existiert nicht oder sein Account ist inaktiv. |

  @api @requestPasswordReset
  Scenario: I can request /api/users/request-password-reset with an disabled user and get a violation
    When I send an api platform "POST" request to "/api/users/request-password-reset" with parameters:
      | key      | value         |
      | username | lonely@gmx.de |
    Then the response should be in JSON
#    And print last JSON response
    And the response status code should be 422
    And the JSON nodes should be equal to:
      | hydra:title                | An error occurred                                                         |
      | violations[0].propertyPath | username                                                                  |
      | violations[0].message      | Der Nutzer "lonely@gmx.de" existiert nicht oder sein Account ist inaktiv. |

  @api @requestPasswordReset
  Scenario: I can request /api/users/request-password-reset with a valid user
    When I send an api platform "POST" request to "/api/users/request-password-reset" with parameters:
      | key      | value       |
      | username | karl@gmx.de |
      | email    | karl@gmx.de |
    And the response status code should be 200
    Then the response should be empty
    And there is an empty confirmationToken for "karl@gmx.de"

  @api @requestPasswordReset
  Scenario: I can request /api/users/request-password-reset with a valid user
    When I send an api platform "POST" request to "/api/users/request-password-reset" with parameters:
      | key      | value       |
      | username | karl@gmx.de |
      | email    |             |
    And the response status code should be 200
    Then the response should be empty
    And there is a non empty confirmationToken for "karl@gmx.de"
