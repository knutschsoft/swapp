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
        $crawler = $client->request('GET', $url);

        $this->assertStatusCode(200, $client, $crawler, $url, $user);
        $this->assertStringContainsString(
            'Id,Name,Beginn,Ende,Reflexion,Bewertung,"systemische Frage","systemische Antwort",',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '"Erkenntnisse, Überlegungen, Zielsetzungen","Termine, Besorgungen, Verabredungen",',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '"Wiedervorlage Dienstberatung",Wetter,Ferien,Tageskonzept,Teamname',
            $client->getResponse()->getContent()
        );
    }

    private function loadUserFixtures(): void
    {
        $this->loadFixtureFiles([
            'fixtures/test/user.yml',
        ]);
    }
}
