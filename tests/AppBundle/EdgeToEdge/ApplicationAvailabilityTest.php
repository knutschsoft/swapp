<?php
namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ApplicationAvailabilityTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $credentials = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $client = static::makeClient($credentials);
        $client->request('GET', $url);
        $this->isSuccessful($client->getResponse());
    }

    public function urlProvider()
    {
        return array(
            array('/walks'),
            array('/tag'),
            array('/createtag'),
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
