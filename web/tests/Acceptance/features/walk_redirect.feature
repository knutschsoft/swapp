Feature: An user can request a non existing walk and get redirected

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email         | roles | client        |
      | lonely@gmx.de |       | client@gmx.de |

  @javascript
  @walkRedirect
  Scenario: I can request a non existing wayPoint as an user without a group and I will get redirected to dashboard
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/0815/wegpunkt-hinzufuegen"
    Then I wait for "Diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet." to appear
    And I should be on "/dashboard"
