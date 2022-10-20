Feature: Testing user create resource

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
      | main@gmx.de   |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | karl@gamer.de     |                  | gamer@gmx.de  |
      | admin@gamer.de    | ROLE_ADMIN       | gamer@gmx.de  |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |

  @api @userCreate
  Scenario: I can request /api/users/create as a not authenticated user and an auth error will occur
    When I send an api platform POST request to "/api/users/create" with parameters:
      | key      | value                    |
      | username | new@gmx.de               |
      | email    | new@gmx.de               |
      | roles    | array<>                  |
      | client   | clientIri<client@gmx.de> |
    Then the response should be in JSON
    And the response status code should be 401
#    And print last JSON response
    And the JSON nodes should be equal to:
      | code | 401 |

  @api @userCreate
  Scenario: I can request /api/users/create as an admin and will create a new user for my client
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform POST request to "/api/users/create" with parameters:
      | key      | value                    |
      | username | newbie                   |
      | email    | new@gmx.de               |
      | roles    | array<>                  |
      | client   | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | @type        | User         |
      | username     | newbie       |
      | email        | new@gmx.de   |
      | isEnabled    | 0            |
      | isSuperAdmin | 0            |
      | roles[0]     | ROLE_USER    |
      | updatedBy    | admin@gmx.de |
      | createdBy    | admin@gmx.de |
    And an email should be sent to "new@gmx.de" with subject:
      """
      Swapp | Account erstellt
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Hallo newbie!
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Dein Account wurde soeben von einem Administrator erstellt.
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Mit Hilfe des folgenden Links kannst Du Deine E-Mail-Adresse bestätigen und Dir ein Passwort generieren:
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      ">http://localhost/email-bestaetigen/
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      <strong>Du hast die Anfrage nicht ausgelöst?</strong>
              Dann kannst Du diese Mail ignorieren und löschen.
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Mit freundlichen Grüßen,<br>
                          Dein Team von Swapp
      """

  @api @userCreate
  Scenario: I can request /api/users/create as non-admin and will not be able to create a new user for my client
    Given I am authenticated against api as "karl@gmx.de"
    When I send an api platform POST request to "/api/users/create" with parameters:
      | key      | value                    |
      | username | new@gmx.de               |
      | email    | new@gmx.de               |
      | roles    | array<ROLE_ADMIN>        |
      | client   | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:title       | An error occurred |
      | hydra:description | Access Denied.    |

  @api @userCreate
  Scenario: I can request /api/users/create as an admin and will not be able to create a superadmin
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform POST request to "/api/users/create" with parameters:
      | key      | value                    |
      | username | new@gmx.de               |
      | email    | new@gmx.de               |
      | roles    | array<ROLE_SUPER_ADMIN>  |
      | client   | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:title       | An error occurred |
      | hydra:description | Access Denied.    |

  @api @userCreate
  Scenario: I can request /api/users/create as an superadmin and will be able to create a superadmin
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform POST request to "/api/users/create" with parameters:
      | key      | value                    |
      | username | newbie                   |
      | email    | new@gmx.de               |
      | roles    | array<ROLE_SUPER_ADMIN>  |
      | client   | clientIri<client@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | @type        | User                   |
      | username     | newbie                 |
      | email        | new@gmx.de             |
      | isEnabled    | 0                      |
      | isSuperAdmin | 1                      |
      | roles[0]     | ROLE_ALLOWED_TO_SWITCH |
      | roles[1]     | ROLE_SUPER_ADMIN       |
      | roles[2]     | ROLE_USER              |
      | updatedBy    | superadmin@gmx.de      |
      | createdBy    | superadmin@gmx.de      |

    And an email should be sent to "new@gmx.de" with subject:
      """
      Swapp | Account erstellt
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Hallo newbie!
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Dein Account wurde soeben von einem Administrator erstellt.
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Mit Hilfe des folgenden Links kannst Du Deine E-Mail-Adresse bestätigen und Dir ein Passwort generieren:
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      ">http://localhost/email-bestaetigen/
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      <strong>Du hast die Anfrage nicht ausgelöst?</strong>
              Dann kannst Du diese Mail ignorieren und löschen.
      """
    And an email should be sent to "new@gmx.de" with content:
      """
      Mit freundlichen Grüßen,<br>
                          Dein Team von Swapp
      """

  @api @userCreate
  Scenario: I can request /api/users/create as an admin and will not be able to create an user for another client
    Given I am authenticated against api as "admin@gmx.de"
    When I send an api platform POST request to "/api/users/create" with parameters:
      | key      | value                   |
      | username | new@gmx.de              |
      | email    | new@gmx.de              |
      | roles    | array<ROLE_SUPER_ADMIN> |
      | client   | clientIri<gamer@gmx.de> |
    Then the response should be in JSON
#    And print last JSON response
    And the JSON nodes should be equal to:
      | hydra:title | An error occurred |
    And the JSON nodes should contain:
      | hydra:description | Item not found for "/api/clients |
