<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

class ApplicationAvailabilityTest extends BaseWebTestCase
{
    /**
     * @dataProvider urlProvider
     *
     * @param string $url
     */
    public function testPageIsSuccessful(string $url): void
    {
        $this->loadAllFixtures();
        $user = 'admin';
        $client = $this->getClient($user);
        $crawler = $client->request('GET', $url);
        $expectedStatusCode = 200;
        $this->assertStatusCode($expectedStatusCode, $client, $crawler, $url, $user);
    }

    public function urlProvider(): array
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

    /**
     * @return \Doctrine\Common\DataFixtures\Executor\AbstractExecutor|void|null
     */
    private function loadAllFixtures()
    {
        $this->loadFixtureFiles(
            [
                'fixtures/test/tag.yml',
                'fixtures/test/guest.yml',
                'fixtures/test/team.yml',
                'fixtures/test/user.yml',
                'fixtures/test/walk.yml',
                'fixtures/test/systemicQuestion.yml',
                'fixtures/test/wayPoint.yml',
            ]
        );
    }
}
