<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

class WalkExportTest extends BaseWebTestCase
{
    public function testWalkExportIsSuccessful(): void
    {
        $this->loadUserFixtures();
        $user = 'admin';
        $client = $this->getClient($user);

        $url = '/walkexport';
        \ob_start();
        $crawler = $client->request('GET', $url);
        \ob_get_clean();

        $this->assertStatusCode(200, $client, $crawler, $url, $user);
    }

    private function loadUserFixtures(): void
    {
        $this->loadFixtureFiles([
            'fixtures/test/user.yml',
        ]);
    }
}
