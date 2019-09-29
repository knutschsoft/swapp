<?php
declare(strict_types=1);

namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WayPointTest extends WebTestCase
{
    public function testWayPointShow(): void
    {
        $this->loadFixtureFiles(
            [
                '@AppBundle/DataFixtures/ORM/test/user.yml',
                '@AppBundle/DataFixtures/ORM/test/guest.yml',
                '@AppBundle/DataFixtures/ORM/test/tag.yml',
                '@AppBundle/DataFixtures/ORM/test/team.yml',
                '@AppBundle/DataFixtures/ORM/test/walk.yml',
                '@AppBundle/DataFixtures/ORM/test/systemicQuestion.yml',
                '@AppBundle/DataFixtures/ORM/test/wayPoint.yml',
            ]
        );

        $credentials = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $client = static::makeClient($credentials);

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
