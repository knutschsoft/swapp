Feature: An user can upload an image when creating a new wayPoint

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email         | roles | client        |
      | lonely@gmx.de |       | client@gmx.de |
    Given the following teams exists:
      | name     | ageRanges | client        |
      | Westhang | 1-10      | client@gmx.de |
    Given the following tags exists:
      | name   | color     | client        |
      | Gewalt | Chocolate | client@gmx.de |
      | Drogen | Blue      | client@gmx.de |
    Given the following walks exists:
      | name    | team     |
      | Gorbitz | Westhang |

  @javascript
  @wayPointImageUpload
  Scenario: I can add an image when I create a wayPoint
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then I wait for 'Wegpunkt zur Runde "Gorbitz" hinzufügen' to appear
    And the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "@image.jpg" in "Bildupload" field
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    And I click on element "button-way-point-submit"
    And I wait for 'Der Wegpunkt "Assieck" wurde erfolgreich zur Runde hinzugefügt.' to appear

    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to disappear
    Then I wait for "Wegpunkt ansehen" to appear
    And I set browser window size to "1000" x "1800"
    When I click on element "button-wegpunkt-ansehen-Assieck"
    Then I wait for "Wegpunkt: Assieck vom" to appear
    Then I wait for "kein Bild hochgeladen" to disappear

  @javascript
  @wayPointImageUpload
  Scenario: I can not add an text file as image when I create a wayPoint
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then I wait for 'Wegpunkt zur Runde "Gorbitz" hinzufügen' to appear
    And the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "@text_file.txt" in "Bildupload" field
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    And I click on element "button-way-point-submit"
    And I wait for 'The provided "data:" URI is not valid.' to appear

  @javascript
  @wayPointImageUpload @ignore # chrome is crashing:   unknown error: session deleted because of page crash from unknown error: cannot determine loading status from tab crashed (Session info: headless chrome=98.0.4758.102)
  Scenario: I can not add a too large image when I create a wayPoint
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walkId<Gorbitz>/wegpunkt-hinzufuegen"
    Then I wait for 'Wegpunkt zur Runde "Gorbitz" hinzufügen' to appear
    And the element "locationName" should be enabled
    When I enter "Assieck" in "locationName" field
    When I enter "Straßenbahnen sind blockiert" in "note" field
    When I enter "@image_big.jpg" in "Bildupload" field
    Then I wait for "Für diese Runde gibt es keine Wegpunkte." to appear
    Then I wait for "Wegpunkt ansehen" to disappear
    And I click on element "button-way-point-submit"
    And I wait for 'Das Bild ist mit 10.08 MB größer als die maximal erlaubten 10 MB.' to appear
