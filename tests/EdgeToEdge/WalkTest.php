<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

class WalkTest extends BaseWebTestCase
{
    private const WAYPOINT_NAME = 'location';

    private const WALK_NAME = 'name';

    public function testCreateWalk(): void
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
        $client->followRedirects(true);

        $url = '/walks';
        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client, $crawler, $url, $user);

        $crawler = $crawler->filter('[data-test="create-walk"]');
        $crawler = $client->click($crawler->link());
        $this->assertStatusCode(200, $client, $crawler, $crawler->getUri(), $user);

        $form = $crawler->filter('[data-test="create-way-point"]')->form();
        $form->setValues(
            [
                'walk_prologue[name]' => self::WALK_NAME,
                'walk_prologue[conceptOfDay]' => 'conceptOfDay',
                'walk_prologue[weather]' => 'Sonne',
            ]
        );
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client, $crawler, $crawler->getUri(), $user);

        $form = $crawler->filter('[data-test="save-and-finish-way-point"]')->form();
        $form->setValues(
            [
                'way_point[locationName]' => self::WAYPOINT_NAME,
                'way_point[note]' => 'note',
            ]
        );
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client, $crawler, $crawler->getUri(), $user);

        $this->assertStringContainsString(
            \sprintf('Wegpunkt %s wurde erfolgreich zur Runde %s hinzugefügt.', self::WAYPOINT_NAME, self::WALK_NAME),
            $crawler->text()
        );

        $form = $crawler->filter('[data-test="create-walk"]')->form();
        $form->setValues(
            [
                'walk[walkReflection]' => 'walkReflectionText',
                'walk[systemicAnswer]' => 'systemicAnswerText',
            ]
        );
        $crawler = $client->submit($form);
        $this->assertStatusCode(200, $client, $crawler, $crawler->getUri(), $user);

        $this->assertStringContainsString(
            'Runde wurde erfolgreich erstellt.',
            $crawler->text()
        );
    }
}
