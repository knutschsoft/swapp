Feature: A user can get a new password on his own

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email             | roles            | isEnabled |client        |
      | old_karl@gmx.de   |                  | 0         |client@gmx.de |
      | karl@gmx.de       |                  | 1         |client@gmx.de |

  @javascript @password_reset
  Scenario: I use "Passwort vergessen?" as a not authenticated user and can set a new password for me
    Given I am on "/anmeldung"
    And there is an empty confirmationToken for "karl@gmx.de"
    And I click on text "Passwort vergessen oder noch kein Passwort?"
    Then I am on "/passwort-zuruecksetzen"
    And I fill in "username" with "karl@gmx.de"
    When I click on text "Passwortänderung beantragen"
    Then I wait for "Herzlichen Glückwunsch!" to appear
    Then I wait for "Du solltest eine Mail bekommen haben." to appear
    Then I wait for "Bitte schaue ggfs. auch in deinem Spam-Ordner nach." to appear
    Then I wait for "Alle weiteren Schritte stehen in der Mail." to appear
    And there is a non empty confirmationToken for "karl@gmx.de"

    Given I am on page passwort-aendern for "karl@gmx.de"
    Then I should see "Passwort ändern" appear
    When I fill in "password" with "Dumpfbacke18"
    And I fill in "passwordRepeat" with "Dumpfbacke18"
    And I click on text "Passwort ändern"
    Then I should see "Herzlichen Glückwunsch!" appear
    And I should see "Du hast erfolgreich dein Passwort geändert." appear
    And I should see "Melde dich jetzt an:" appear
    And I should see "Zur Anmeldung" appear

    When I click on text "Zur Anmeldung"
    Then I should see "Login" appear

    And I should be on "/anmeldung"
    When I enter "karl@gmx.de" in "username" field
    When I enter "Dumpfbacke18" in "password" field
    When I click on text "Anmelden"
    Then I wait for "Neue Streetwork-Runde" to appear

  @javascript @password_reset
  Scenario: I use "Passwort vergessen?" as a not authenticated user and can set a new password for me
    Given I am authenticated as "karl@gmx.de"
    And there is an empty confirmationToken for "karl@gmx.de"
    When I go to "/passwort-aenderung-beantragen"
    Then I should see "Um dein Passwort zu ändern, drücke bitte folgenden Knopf." appear
    When I click on text "Neues Passwort beantragen"
    Then I wait for "Herzlichen Glückwunsch!" to appear
    Then I wait for "Du solltest eine Mail bekommen haben." to appear
    Then I wait for "Bitte schaue ggfs. auch in deinem Spam-Ordner nach." to appear
    Then I wait for "Alle weiteren Schritte stehen in der Mail." to appear
    And there is a non empty confirmationToken for "karl@gmx.de"

    Given I am on page passwort-aendern for "karl@gmx.de"
    Then I should see "Passwort ändern" appear
    When I fill in "password" with "Dumpfbacke18"
    And I fill in "passwordRepeat" with "Dumpfbacke18"
    Then I wait for field "btn-change-password" to be not disabled
    And I click on element "btn-change-password"
    Then I should see "Herzlichen Glückwunsch!" appear
    And I should see "Du hast erfolgreich dein Passwort geändert." appear
    And I should see "Melde dich jetzt an:" disappear
    And I should see "Zur Anmeldung" disappear

  @javascript @password_reset @disabled
  Scenario: I use "Passwort vergessen?" as a not authenticated and disabled user I can not set a new password for me
    Given I am on "/anmeldung"
    And there is an empty confirmationToken for "old_karl@gmx.de"
    And I click on text "Passwort vergessen oder noch kein Passwort?"
    Then I am on "/passwort-zuruecksetzen"
    And I fill in "username" with "old_karl@gmx.de"
    When I click on text "Passwortänderung beantragen"
    Then I wait for 'Der Nutzer "old_karl@gmx.de" existiert nicht oder sein Account ist inaktiv.' to appear
    Then I wait for "Herzlichen Glückwunsch!" to disappear
    Then I wait for "Du solltest eine Mail bekommen haben." to disappear
    Then I wait for "Bitte schaue ggfs. auch in deinem Spam-Ordner nach." to disappear
    Then I wait for "Alle weiteren Schritte stehen in der Mail." to disappear
    And there is an empty confirmationToken for "old_karl@gmx.de"
