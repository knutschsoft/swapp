Feature: Testing client create resource with rating image

  Background:
    Given the following clients exists:
      | email         |
      | client@gmx.de |
      | main@gmx.de   |
    Given the following users exists:
      | email             | roles            | client        |
      | karl@gmx.de       |                  | client@gmx.de |
      | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
      | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |

  @api @apiClientCreate
  Scenario: I can request /api/clients/create as a superadmin and create a client with ratingImage
    Given I am authenticated against api as "superadmin@gmx.de"
    When I send an api platform "POST" request to "/api/clients/create" with parameters:
      | key                 | value                 |
      | client      | clientIri<client@gmx.de> |
      | name                | Streetworkers         |
      | email               | newclient@example.com |
      | description         | doing good stuff      |
      | ratingImageFileName | AreYouDrunk.jpg       |
      | ratingImageFileData | @image.jpg            |
#    And print last JSON response
    Then the response should be in JSON
    And the response status code should be 200
    And the enriched JSON nodes should be equal to:
      | @type             | Client                |
      | name              | Streetworkers         |
    And the JSON nodes should contain:
      | ratingImageName | _AreYouDrunk.jpg                       |
      | ratingImageSrc  | http://localhost/images/client_rating/ |
      | ratingImageSrc  | _AreYouDrunk.jpg                       |
