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
