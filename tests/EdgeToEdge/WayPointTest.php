<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

class WayPointTest extends BaseWebTestCase
{
    public function testWayPointShow(): void
    {
        $this->loadFixtureFiles(
            [
                'fixtures/test/user.yml',
                'fixtures/test/guest.yml',
                'fixtures/test/tag.yml',
                'fixtures/test/team.yml',
                'fixtures/test/walk.yml',
                'fixtures/test/systemicQuestion.yml',
                'fixtures/test/wayPoint.yml',
            ]
        );

        $user = 'admin';
        $client = $this->getClient($user);

        $url = '/walks';
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client, $crawler, $url, $user);

        // link in first table which is listing walks
        $crawler = $crawler->filter('table td a');

        $crawler = $client->click($crawler->link());

        $this->assertStatusCode(200, $client, $crawler, $crawler->getUri(), $user);

        // link in first table which is listing wayPoints
        $crawler = $crawler->filter('table td a');

        $client->click($crawler->link());

        $this->assertStatusCode(200, $client, $crawler, $crawler->getUri(), $user);
    }
}
