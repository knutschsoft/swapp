<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WalkTest extends WebTestCase
{
    private const WAYPOINT_NAME = 'location';

    private const WALK_NAME = 'name';

    public function testCreateWalk(): void
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
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/walks');
        $this->isSuccessful($client->getResponse());

        $crawler = $crawler->filter('[data-test="create-walk"]');
        $crawler = $client->click($crawler->link());
        $this->isSuccessful($client->getResponse());

        $form = $crawler->filter('[data-test="create-way-point"]')->form();
        $form->setValues(
            [
                'walk_prologue[name]' => self::WALK_NAME,
                'walk_prologue[conceptOfDay]' => 'conceptOfDay',
                'walk_prologue[weather]' => 'Sonne',
            ]
        );
        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        $form = $crawler->filter('[data-test="save-and-finish-way-point"]')->form();
        $form->setValues(
            [
                'way_point[locationName]' => self::WAYPOINT_NAME,
                'way_point[note]' => 'note',
            ]
        );
        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        $this->assertContains(
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
        $this->isSuccessful($client->getResponse());

        $this->assertContains(
            'Runde wurde erfolgreich erstellt.',
            $crawler->text()
        );
    }
}
