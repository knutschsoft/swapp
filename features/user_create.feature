Feature:
  As an admin I can create a new user

  Background:
    Given the following users exists:
      | username | roles                |
      | admin    | ROLE_ADMIN ROLE_USER |

  Scenario: I can create a new user in backend
    When I am authenticated as "admin"
    Given I am on '/eadmin'
    Then I wait for 'Benutzer' to appear
    When I click on text "Benutzer"
    Then I wait for 'Benutzer erstellen' to appear
    When I click on text "Benutzer erstellen"
    When I fill in "Username" with "Karl"
    When I fill in "E-Mail" with "Karl@Karl.de"
    When I fill in "Plain password" with "Karl"
    And I check "Enabled"
    And I click on text "Änderungen speichern"
    Then I am authenticated as "Karl"
