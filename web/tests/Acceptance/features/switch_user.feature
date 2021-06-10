Feature: An superadmin can impersonate any user

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email             | roles            | isEnabled | client        |
      | old_karl@gmx.de   |                  | 0         | client@gmx.de |
      | karl@gmx.de       |                  | 1         | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | 1         | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | 1         | client@gmx.de |

  @javascript
  @switchUser
  Scenario: I can switch user when I am a superadmin
    Given I am authenticated as "superadmin@gmx.de"
    Given I am on "/benutzer"
    And I wait for "Nutzer wechseln" to appear

    And I click on element "switch-user-karl@gmx.de"

    Given I am on "/dashboard"
    And I wait for "Neue Streetwork-Runde" to appear
    Then I should see "karl@gmx.de" appear

    When I click on element "nav-user-item"
    Then I should see "Nutzerwechsel beenden" appear

    When I click on element "exit-switch-user"
    Then I should see "karl@gmx.de" disappear
    And I should see "superadmin@gmx.de" appear

    When I click on element "nav-user-item"
    Then I should see "Nutzerwechsel beenden" disappear
    Then I should see "Nutzerwechsel" appear

  @javascript
  @switchUser
  Scenario: I can not switch user when I am just an admin
    Given I am authenticated as "admin@gmx.de"
    Given I am on "/dashboard"

    When I click on element "nav-user-item"
    Then I should see "Was ist Swapp?" appear
    Then I should see "Nutzerwechsel" disappear
