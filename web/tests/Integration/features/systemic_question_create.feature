Feature: Testing systemic question resource

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
            | gamer@gmx.de  |
            | main@gmx.de   |
        Given the following users exists:
            | email             | roles            | client        |
            | karl@gmx.de       |                  | client@gmx.de |
            | karl@gamer.de     |                  | gamer@gmx.de  |
            | admin@gmx.de      | ROLE_ADMIN       | client@gmx.de |
            | superadmin@gmx.de | ROLE_SUPER_ADMIN | main@gmx.de   |
        Given the following systemic questions exists:
            | question                | client        |
            | Is Gewalt good for you? | client@gmx.de |
            | Is Drogen good for you? | client@gmx.de |
            | Is RPG good for you?    | gamer@gmx.de  |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as a not authenticated user and an auth error will occur
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                    |
            | question | PowderBlue               |
            | client   | clientIri<client@gmx.de> |
        Then the response should be in JSON
        And the response status code should be 401
#    And print last JSON response
        And the JSON nodes should be equal to:
            | code | 401 |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as authenticated user and will get access denied
        Given I am authenticated against api as "karl@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                    |
            | question | PowderBlue               |
            | client   | clientIri<client@gmx.de> |
        Then the response should be in JSON
        And the response status code should be 403
#    And print last JSON response
        And the JSON nodes should be equal to:
            | description | Access Denied. |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an admin for another client and will get access denied
        Given I am authenticated against api as "admin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                   |
            | question | Religion                |
            | client   | clientIri<gamer@gmx.de> |
        Then the response should be in JSON
#    And print last JSON response
        And the response status code should be 400
        And the JSON nodes should be equal to:
            | title | An error occurred |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an admin and will get access denied
        Given I am authenticated against api as "admin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                    |
            | question | Religion                 |
            | client   | clientIri<client@gmx.de> |
        Then the response should be in JSON
        And the response status code should be 200
#    And print last JSON response
        And the JSON nodes should be equal to:
            | question | Religion |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an superadmin and will create a new systemic question
        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                    |
            | question | Religion                 |
            | client   | clientIri<client@gmx.de> |
        Then the response should be in JSON
        And the response status code should be 200
#    And print last JSON response
        And the JSON nodes should be equal to:
            | question | Religion |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an superadmin with invalid color and will get a validation error
        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                    |
            | question | ab                       |
            | client   | clientIri<client@gmx.de> |
        Then the response should be in JSON
#    And print last JSON response
        And the response status code should be 422
        And the JSON nodes should be equal to:
            | violations[0].propertyPath | question                                                               |
            | violations[0].message      | Diese Zeichenkette ist zu kurz. Sie sollte mindestens 5 Zeichen haben. |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an superadmin with empty color and name and will get a validation error
        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                    |
            | question |                          |
            | client   | clientIri<client@gmx.de> |
        Then the response should be in JSON
#    And print last JSON response
        And the response status code should be 422
        And the JSON nodes should be equal to:
            | violations[0].propertyPath | question                            |
            | violations[0].message      | Dieser Wert sollte nicht leer sein. |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an superadmin with nulled color and name and will get a validation error
        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                    |
            | question | <null>                   |
            | client   | clientIri<client@gmx.de> |
        Then the response should be in JSON
#    And print last JSON response
        And the response status code should be 400
        And the JSON nodes should be equal to:
            | description | The input data is misformatted. |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an superadmin with unset color and name and will get a validation error
        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key    | value                    |
            | client | clientIri<client@gmx.de> |
        Then the response should be in JSON
#    And print last JSON response
        And the response status code should be 422
        And the JSON nodes should be equal to:
            | violations[0].propertyPath | question                            |
            | violations[0].message      | Dieser Wert sollte nicht leer sein. |

    @api @apiSystemicQuestionCreate
    Scenario: I can request /api/systemic_questions/create as an superadmin with already existing name and will get a validation error
        Given I am authenticated against api as "superadmin@gmx.de"
        When I send an api platform "POST" request to "/api/systemic_questions/create" with parameters:
            | key      | value                   |
            | question | Drogen                  |
            | client   | clientIri<gamer@gmx.de> |
        Then the response should be in JSON
#    And print last JSON response
        And the response status code should be 200
        And the JSON nodes should be equal to:
            | question | Drogen |
