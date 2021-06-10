Feature: A user can do a walk

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
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
      | RPG    | Lime      | gamer@gmx.de  |

  @javascript
  @tagList
  Scenario: I can not see tag list as a non admin user
    Given I am authenticated as "lonely@gmx.de"
    When I am on "tags"
    Then I should be on "/dashboard"

  @javascript
  @tagList
  Scenario: I can not see tag list as an admin user
    Given I am authenticated as "admin@gmx.de"
    When I am on "tags"
    Then I should be on "/tags"

    And I wait for "Liste der Tags" to appear
    And I wait for "Name" to appear
    And I wait for "Farbe" to appear
    And I wait for "Klient" to disappear
    And I wait for "RPG" to disappear
    And I wait for "Gewalt" to appear
    And I wait for "Chocolate" to appear
    And I wait for "Drogen" to appear
    And I wait for "blue" to appear

    And I wait for "Neuen Tag erstellen" to appear
    # open
    And I click on element "header-tag-create"
    And I wait for "PowderBlue" to appear
    And I wait for "client@gmx.de" to disappear

  @javascript
  @tagList
  Scenario: I can see tag list as an super admin user
    Given I am authenticated as "superadmin@gmx.de"
    When I am on "tags"
    Then I should be on "/tags"

    And I wait for "Liste der Tags" to appear
    And I wait for "Name" to appear
    And I wait for "Farbe" to appear
    And I wait for "Klient" to appear
    And I wait for "RPG" to appear
    And I wait for "Gewalt" to appear
    And I wait for "Chocolate" to appear
    And I wait for "Drogen" to appear
    And I wait for "blue" to appear

    And I wait for "Neuen Tag erstellen" to appear
    # open
    And I click on element "header-tag-create"
    And I wait for "PowderBlue" to appear
    And I wait for "client@gmx.de" to appear
    And I wait for "gamer@gmx.de" to appear
