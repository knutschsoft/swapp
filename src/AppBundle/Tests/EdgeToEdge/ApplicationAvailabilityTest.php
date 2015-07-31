<?php
namespace AppBundle\Tests\EdgeToEdge;

class ApplicationAvailabilityFunctionalTest extends BaseWebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $this->logIn();
        $this->client->request('GET', $url);

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code is ' . $this->client->getResponse()->getStatusCode()
        );
    }

    public function urlProvider()
    {
        return array(
            array('/walks'),
            array('/tag'),
            array('/createtag'),
            array('/tagcreated'),
            array('/walkcreated'),
//            array('/waypoint'), @TODO enable as soon as WayPointController::homeScreenAction is finished
            array('/createwaypoint'),
            array('/waypointcreated'),
            array('/eadmin/?action=list&entity=Team'),
            array('/eadmin/?action=list&entity=Walk'),
            array('/eadmin/?action=list&entity=WayPoint'),
            array('/eadmin/?action=list&entity=Tag'),
            array('/eadmin/?action=list&entity=User'),
            array('/eadmin/?action=list&entity=Guest'),
            array('/eadmin/?action=list&entity=SystemicQuestion'),
            // ...
        );
    }
}
