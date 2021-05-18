Feature: An user can request a non existing wayPoint and get redirected

  Background:
    Given the following users exists:
      | email         | roles |
      | lonely@gmx.de |       |
    Given the following teams exists:
      | name     | ageRanges |
      | Westhang | 1-10      |
    Given the following tags exists:
      | name   | color |
      | Gewalt | Green |
      | Drogen | Blue  |
    Given the following walks exists:
      | name    | team     |
      | Gorbitz | Westhang |

  @javascript
  @wayPointImageUpload
  Scenario: I can request a non existing wayPoint as an user without a group and I will get redirected to dashboard
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walk<Gorbitz>/wegpunkt-hinzufuegen"
    Then I wait for 'Wegpunkt zur Runde "Gorbitz" hinzufügen' to appear
    And the element "Ort" should be enabled
    When I enter "Assieck" in "Ort" field
    When I enter "Straßenbahnen sind blockiert" in "Beobachtung" field
    When I enter "@image.jpg" in "Bildupload" field
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    And I click on element "save-way-point"
    And I wait for "Wegpunkt erfolgreich hinzugefügt." to appear

    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to disappear
    Then I wait for "Wegpunkt ansehen" to appear
    And I set browser window size to "1000" x "1800"
    When I click on element "button-wegpunkt-ansehen-Assieck"
    Then I wait for "Wegpunkt: Assieck vom" to appear
    Then I wait for "Bild: kein Bild hochgeladen" to disappear
