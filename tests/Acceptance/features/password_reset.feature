#Feature: An existing user can get a new password on his own
#
#  Background:
#    Given the following users exists:
#      | email       |
#      | karl@gmx.de |
#
#  @dashboard
#  @javascript
#  Scenario: I use "Passwort vergessen?" to set a new password for me
#    Given I am on "/"
#    Then I wait for "Willkommen!" to appear
#    When I enter "KARL@gmx.de" in username field
#    When I click on text "Weiter"
#    Then I wait for "Willkommen zurück!" to appear
#    And I click on text "Passwort vergessen?"
#    Then I am on "/passwort-zuruecksetzen"
#    And I fill in "username" with "karl@gmx.de"
#    When I click on text "Passwortänderung beantragen"
#    Then I wait for "Herzlichen Glückwunsch!" to appear
#    Then I wait for "Du solltest eine Mail bekommen haben." to appear
#    Then I wait for "Bitte schaue ggfs. auch in deinem Spam-Ordner nach." to appear
#    Then I wait for "Alle weiteren Schritte stehen in der Mail." to appear
##    And there is a non empty confirmationToken for "karl@gmx.de"
##
#    Given I am on page passwort-aendern for "karl@gmx.de"
#    Then I should see "Passwort ändern" appear
#    When I fill in "password" with "Dumpfbacke18"
#    And I fill in "passwordRepeat" with "Dumpfbacke18"
#    And I click on text "Passwort ändern"
#    Then I should see "Herzlichen Glückwunsch!" appear
#    And I should see "Du hast erfolgreich dein Passwort geändert." appear
#    And I should see "Melde dich jetzt an:" appear
#    And I should see "Zur Anmeldung" appear
#
#    When I click on text "Zur Anmeldung"
#    Then I should see "Willkommen" appear
#    When I enter "KARL@gmx.de" in username field
#    When I click on text "Weiter"
#    Then I wait for "Willkommen zurück!" to appear
#    When I enter "Dumpfbacke18" in "password" field
#    When I click on text "Anmelden"
#    Then I wait for "DIE ZAHLEN" to appear
#
##    Then an email should be sent to "karl@gmx.de" with content:
##      """
##       narf
##      """
