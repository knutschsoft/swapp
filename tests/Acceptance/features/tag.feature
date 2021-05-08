Feature: A user can do a walk

  Background:
    Given the following users exists:
      | email             | roles            |
      | karl@gmx.de       |                  |
      | lonely@gmx.de     |                  |
      | admin@gmx.de      | ROLE_ADMIN       |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN |
    Given the following teams exists:
      | name     | users       | ageRanges          |
      | Westhang | karl@gmx.de | 1-10,3-12, 13 - 90 |
    Given the following systemic questions exists:
      | question |
      | How old? |
    Given the following tags exists:
      | name   | color |
      | Gewalt | Green |
      | Drogen | Blue  |

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
    Then I should be on "/dashboard"

  @javascript
  @tagList
  Scenario: I can see tag list as an super admin user
    Given I am authenticated as "superadmin@gmx.de"
    When I am on "tags"
    Then I should be on "/tags"

    And I wait for "Liste der Tags" to appear
    And I wait for "Name" to appear
    And I wait for "Farbe" to appear
    And I wait for "Gewalt" to appear
    And I wait for "Green" to appear
    And I wait for "Drogen" to appear
    And I wait for "blue" to appear

    And I wait for "Neuen Tag erstellen" to appear
    # open
    And I click on element "header-tag-create"
    And I wait for "PowderBlue" to appear

  @javascript
  @tagCreate
  Scenario: I can create a new tag as an super admin user
    Given I am authenticated as "superadmin@gmx.de"
    When I am on "tags"
    And I wait for "Liste der Tags" to appear
    And I click on text "Liste der Tags"
    And I wait for "ID" to disappear

    # open
    And I click on element "header-tag-create"

    Then the element "button-tag-create" should be enabled
    When I enter "Religion" in "name" field
    When I click on text "MediumAquaMarine"
    Then I click on element "button-tag-create"

    And I click on text "Liste der Tags"
    # close create collapse
    And I click on element "header-tag-create"
    And I wait for "Liste der Tags" to appear
    And I wait for "ID" to appear
    And I wait for "Name" to appear
    And I wait for "Farbe" to appear
    And I wait for "Gewalt" to appear
    And I wait for "Green" to appear
    And I wait for "Drogen" to appear
    And I wait for "Religion" to appear
    And I wait for "MediumAquaMarine" to appear
