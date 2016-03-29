<?php
namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WayPointTest extends WebTestCase
{
    public function testWayPointShow()
    {
        $this->loadFixtureFiles(
            [
                '@AppBundle/DataFixtures/ORM/user.yml',
                '@AppBundle/DataFixtures/ORM/guest.yml',
                '@AppBundle/DataFixtures/ORM/tag.yml',
                '@AppBundle/DataFixtures/ORM/team.yml',
                '@AppBundle/DataFixtures/ORM/walk.yml',
                '@AppBundle/DataFixtures/ORM/systemicQuestion.yml',
                '@AppBundle/DataFixtures/ORM/wayPoint.yml',
            ]
        );

        $client = static::makeClient(true);

        $crawler = $client->request('GET', '/walks');
        $this->isSuccessful($client->getResponse());

        // link in first table which is listing walks
        $crawler = $crawler->filter('table td a');

        $crawler = $client->click($crawler->link());

        $this->isSuccessful($client->getResponse());

        // link in first table which is listing wayPoints
        $crawler = $crawler->filter('table td a');

        $client->click($crawler->link());

        $this->isSuccessful($client->getResponse());
    }
}
