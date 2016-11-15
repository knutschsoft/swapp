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
        return [
            ['/walks'],
            ['/tag'],
            ['/createtag'],
            ['/eadmin/?action=list&entity=Team'],
            ['/eadmin/?action=list&entity=Walk'],
            ['/eadmin/?action=list&entity=WayPoint'],
            ['/eadmin/?action=list&entity=Tag'],
            ['/eadmin/?action=list&entity=User'],
            ['/eadmin/?action=list&entity=Guest'],
            ['/eadmin/?action=list&entity=SystemicQuestion'],
            // ...
        ];
    }
}
