Feature: An user can access api docs

  @api
  Scenario: I open api page and see api dashboard
    Given I am on "/api/docs"
    Then I wait for "OAS3" to appear
