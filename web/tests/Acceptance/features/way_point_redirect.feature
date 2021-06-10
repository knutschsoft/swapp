Feature: An user can request a non existing wayPoint and get redirected

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
    Given the following users exists:
      | email         | roles | client        |
      | lonely@gmx.de |       | client@gmx.de |

  @javascript
  @wayPointRedirect
  Scenario: I can request a non existing wayPoint as an user without a group and I will get redirected to dashboard
    Given I am authenticated as "lonely@gmx.de"
    And I go to swapp page "/runde/0815/wegpunkt/0815/detail"
    Then I wait for "Dieser Wegpunkt oder diese Runde existiert nicht. Du wurdest auf das Dashboard weitergeleitet." to appear
    And I should be on "/dashboard"
