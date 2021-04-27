Feature: A user can login

  Background:
    Given the following users exists:
      | email             | roles            |
      | karl@gmx.de       |                  |
      | admin@gmx.de      | ROLE_ADMIN       |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN |

  @javascript
  @login
  Scenario: I open dashboard page and login with existing user
    Given I am on "/"
    Then I wait for "Login" to appear
    When I enter "karl@gmx.de" in "username" field
    When I enter "karl@gmx.de" in "password" field
    When I click on text "Anmelden"
    Then I wait for "Neue Streetwork-Runde" to appear

  @javascript
  @login
  Scenario: I open dashboard page and enter wrong credentials to see a failed message
    Given I am on "/"
    Then I wait for "Login" to appear
    When I enter "grafcox@gmx.de" in "username" field
    When I enter "grafcox@gmx.de" in "password" field
    When I click on text "Anmelden"
    Then I wait for "Die Kombination aus E-Mail-Adresse und Passwort ist ungültig." to appear

  @javascript
  @login
  Scenario: I login as admin and see menu
    Given I am on "/"
    Then I wait for "Login" to appear
    When I enter "admin@gmx.de" in "username" field
    When I enter "admin@gmx.de" in "password" field
    When I click on text "Anmelden"
    Then I wait for "Dashboard" to appear
    Then I wait for "Teams" to appear
    Then I wait for "Benutzer" to appear
    Then I wait for "Tags" to appear

  @javascript
  @login
  Scenario: I login as superadmin and see advanced menu
    Given I am on "/"
    Then I wait for "Login" to appear
    When I enter "superadmin@gmx.de" in "username" field
    When I enter "superadmin@gmx.de" in "password" field
    When I click on text "Anmelden"
    Then I wait for "Dashboard" to appear
    Then I wait for "Teams" to appear
    Then I wait for "Benutzer" to appear
    Then I wait for "Tags" to appear
