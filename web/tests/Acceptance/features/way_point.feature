Feature: An user can request a non existing wayPoint and get redirected

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
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
    Given the following walks exists:
      | name    | team     |
      | Gorbitz | Westhang |
    Given the following way points exists:
      | locationName | walkName | beobachtung             | einzelgespraech |
      | Elbamare     | Gorbitz  | Nichts großartig neues. | Jugo geht ab.   |
      | Block17      | Gorbitz  |                         |                 |

  @javascript
  @wayPoint
  Scenario: I can see wayPoint as an user without a group
    Given I am authenticated as "lonely@gmx.de"
    And I should be on "/dashboard"
    Then I wait for "Gorbitz" to appear

    When I click on element "button-runde-ansehen-Gorbitz"
    Then I wait for 'Streetwork-Runde: "Gorbitz"' to appear
    Then I wait for 'Wegpunkte der Runde "Gorbitz"' to appear
    Then I wait for "Elbamare" to appear
    Then I wait for "Block17" to appear
    Then I wait for "Wegpunkt ansehen" to appear

    When I click on element "button-wegpunkt-ansehen-Elbamare"
    Then I wait for "Wegpunkt: Elbamare" to appear
    Then I wait for "Runde: Gorbitz" to appear
    Then I wait for "Ort: Elbamare" to appear
    Then I wait for "Beobachtung: Nichts großartig neues." to appear
    Then I wait for "Einzelgespräch: Jugo geht ab." to appear
    Then I wait for "Bild: kein Bild hochgeladen" to appear
    Then I wait for "1-10m: 0" to appear
    Then I wait for "1-10w: 0" to appear
    Then I wait for "1-10x: 0" to appear
    Then I wait for "13-90m: 0" to appear
    Then I wait for "13-90w: 0" to appear
    Then I wait for "13-90x: 0" to appear

  @javascript
  @wayPoint
  Scenario: I can request a wayPoint route directly as an user without a group
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/walk<Gorbitz>/wegpunkt/wayPoint<Elbamare>/detail"
    Then I wait for "Wegpunkt: Elbamare" to appear
    Then I wait for "Runde: Gorbitz" to appear
    Then I wait for "Ort: Elbamare" to appear
    Then I wait for "Beobachtung: Nichts großartig neues." to appear
    Then I wait for "Einzelgespräch: Jugo geht ab." to appear
    Then I wait for "Bild: kein Bild hochgeladen" to appear
    Then I wait for "1-10m: 0" to appear
    Then I wait for "1-10w: 0" to appear
    Then I wait for "1-10x: 0" to appear
    Then I wait for "13-90m: 0" to appear
    Then I wait for "13-90w: 0" to appear
    Then I wait for "13-90x: 0" to appear
