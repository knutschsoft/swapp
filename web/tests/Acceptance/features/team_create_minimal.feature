Feature: An admin can create a team

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | gamer@gmx.de  |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | lonely@gmx.de     |                  | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | client@gmx.de |
    Given the following teams exists:
      | name     | users       | ageRanges          | client        |
      | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 | client@gmx.de |
    Given the following systemic questions exists:
      | question | client        |
      | How old? | client@gmx.de |

  @javascript
  @teamCreate
  Scenario: I can create a new team as a super admin user
    Given I am authenticated as "superadmin@gmx.de"
    When I am on "teams"
    And I wait for "Liste der Teams" to appear
    And I wait for "Westhang" to appear

    And I click on text "Liste der Teams"
    And I wait for "Westhang" to disappear

    # open
    And I click on element "header-team-create"
    Then I wait for "Allgemeine Daten des Teams" to appear

    When I enter "clientIri<gamer@gmx.de>" in "clients" field
    Then I wait for "Dieser Klient hat noch keine Benutzer." to appear

    When I enter "clientIri<client@gmx.de>" in "clients" field
    Then I wait for "Dieser Klient hat noch keine Benutzer." to disappear

    Then the element "button-team-form" should be disabled
    When I enter "Superteam" in "name" field
    When I click on text "karl@gmx.de"
    Then the element "button-team-form" should be enabled
    Then I click on element "button-team-form"

    And I click on text "Liste der Teams"
    # close create collapse
    And I click on element "header-team-create"
    And I wait for "Liste der Teams" to appear
    And I wait for "Team bearbeiten" to appear
    And I can find the following teams in database:
      | name      | client        |
      | Superteam | client@gmx.de |

  @javascript
  @teamCreate
  Scenario: I can create a new team as an admin user
    Given I am authenticated as "admin@gmx.de"
    When I am on "teams"
    And I wait for "Liste der Teams" to appear
    And I wait for "Westhang" to appear

    And I click on text "Liste der Teams"
    And I wait for "Westhang" to disappear

    # open
    And I click on element "header-team-create"
    Then I wait for "Allgemeine Daten des Teams" to appear

    Then the element "button-team-form" should be disabled
    When I enter "Superteam" in "name" field
    When I click on text "karl@gmx.de"
    Then the element "button-team-form" should be enabled
    Then I click on element "button-team-form"

    And I click on text "Liste der Teams"
    # close create collapse
    And I click on element "header-team-create"
    And I wait for "Liste der Teams" to appear
    And I wait for "Team bearbeiten" to appear
    And I can find the following teams in database:
      | name      | client        |
      | Superteam | client@gmx.de |
