Feature: An user can use way point list

    Background:
        Given the following clients exists:
            | email         |
            | client@gmx.de |
        Given the following users exists:
            | email       | roles      | client        |
            | karl@gmx.de | ROLE_ADMIN | client@gmx.de |
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
            | name    | team     | startTime       | endTime          | isUnfinished | reflection | systemicAnswer | commitments | insights |
            | Klippe1 | Westhang | 01.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
            | Klippe2 | Westhang | 02.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
            | Klippe3 | Westhang | 03.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
            | Klippe4 | Westhang | 04.01.2021 7:20 | 10.01.2021 09:00 |              | zorp       | zorp           | zorp        | zorp     |
        Given the following way points exists:
            | locationName | walkName | tags           | visitedAt       |
            | Assieck      | Klippe1  | Gewalt         | 01.01.2021 7:21 |
            | Assieck2     | Klippe1  | Drogen         | 01.01.2021 7:22 |
            | Assieck3     | Klippe1  | Drogen, Gewalt | 01.01.2021 7:23 |
            | Assieck4     | Klippe1  | Drogen, Gewalt | 01.01.2021 7:24 |
            | Assieck5     | Klippe1  | Drogen, Gewalt | 01.01.2021 7:25 |
            | Assieck6     | Klippe2  | Drogen, Gewalt | 01.01.2021 7:26 |
            | Assieck7     | Klippe2  | Drogen, Gewalt | 01.01.2021 7:27 |
            | Assieck8     | Klippe2  | Drogen, Gewalt | 01.01.2021 7:28 |
            | Assieck9     | Klippe2  | Drogen, Gewalt | 01.01.2021 7:29 |
            | Assieck10    | Klippe2  | Drogen, Gewalt | 01.01.2021 7:30 |
            | Assieck11    | Klippe3  | Drogen, Gewalt | 01.01.2021 7:31 |
            | Assieck12    | Klippe3  | Drogen, Gewalt | 01.01.2021 7:32 |
            | Assieck13    | Klippe4  | Drogen, Gewalt | 01.01.2021 7:33 |
            | Assieck14    | Klippe4  | Drogen, Gewalt | 01.01.2021 7:34 |
            | Assieck15    | Klippe4  | Drogen, Gewalt | 01.01.2021 7:35 |
            | Assieck16    | Klippe4  | Drogen, Gewalt | 01.01.2021 7:36 |
            | Assieck17    | Klippe4  | Drogen, Gewalt | 01.01.2021 7:37 |

    @javascript
    @wayPointList
    Scenario: I can see total items of filtered walk table and also use pagination with default filter of "Beginn"
        And there are exactly 4 walks in database
        And there are exactly 17 wayPoints in database
        Given I am authenticated as "karl@gmx.de"
        Then I wait for 'Liste aller Wegpunkte (17)' to appear

        Then I wait for "Assieck17" to appear
        Then I wait for "Assieck8 " to disappear

        When I click on aria label "Go to page 2"

        Then I wait for "Assieck17" to disappear
        Then I wait for "Assieck8 " to appear
        And I wait for aria label "Go to page 2" to be active

    # prevent page from totalRows filter issue
        When I go to "/dashboard"
        Then I wait for "Assieck17" to disappear
        Then I wait for "Assieck8 " to appear
        And I wait for aria label "Go to page 2" to be active

        When I click on element "reset-way-point-filter"
        Then I wait for "Assieck17" to appear
        Then I wait for "Assieck8 " to disappear
