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
      | Drogen | Blue      | gamer@gmx.de  |

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
    Then I wait for "Name" to appear

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
    And I wait for "Chocolate" to appear
    And I wait for "Drogen" to appear
    And I wait for "Religion" to appear
    And I wait for "MediumAquaMarine" to appear

  @javascript
  @tagCreate
  Scenario: I can create a new tag as an admin user
    Given I am authenticated as "admin@gmx.de"
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
    And I wait for "Chocolate" to appear
    And I wait for "Drogen" to appear
    And I wait for "Religion" to appear
    And I wait for "MediumAquaMarine" to appear
