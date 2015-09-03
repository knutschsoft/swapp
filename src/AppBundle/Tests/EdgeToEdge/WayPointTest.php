<?php
namespace AppBundle\Tests\EdgeToEdge;

class WayPointTest extends BaseWebTestCase
{
    public function testWayPointShow()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/walks');

        $crawler = $crawler->filter('table td a');

        $crawler = $this->client->click($crawler->link());

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code of "Runde ansehen" is ' . $this->client->getResponse()->getStatusCode()
        );

        $crawler = $crawler->filter('table td a');

        $this->client->click($crawler->link());

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code of "Wegpunkt ansehen" is ' . $this->client->getResponse()->getStatusCode()
        );
    }
}
