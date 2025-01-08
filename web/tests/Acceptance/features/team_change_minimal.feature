Feature: An admin can change a team

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
    @teamChange
    Scenario: I can change an existing team as an admin user
        Given I am authenticated as "admin@gmx.de"
        When I am on "/teams"
        And I wait for "Liste der Teams" to appear
        And I wait for "Westhang" to appear

        When I click on text "Team bearbeiten"
        And I set browser window size to "1200" x "2000"
        Then I wait for "Allgemeine Daten des Teams" to appear

        Then the element "button-team-form-change" should be enabled
        When I enter "Superteam" in "name-change" field
        When I click on test element "change-users-karl@gmx.de"
        When I click on test element "change-users-lonely@gmx.de"
        Then the element "button-team-form-change" should be enabled
        Then I click on test element "button-team-form-change"
        Then I wait for "Das Team Superteam wurde erfolgreich geändert." to appear

        And I can find the following teams in database:
            | name      | client        | walkTeamMembers |
            | Superteam | client@gmx.de | lonely@gmx.de   |
