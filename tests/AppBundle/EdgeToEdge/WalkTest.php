<?php
namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WalkTest extends WebTestCase
{
    public function testCreateWalk()
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
        $client->followRedirects(true);

        $crawler = $client->request('GET', '/walks');
        $this->isSuccessful($client->getResponse());

        $crawler = $crawler->filter('[data-test="create-walk"]');
        $crawler = $client->click($crawler->link());
        $this->isSuccessful($client->getResponse());

        $form = $crawler->filter('[data-test="create-way-point"]')->form();
        $form->setValues(
            [
                'app_create_walk_prologue[name]' => 'name',
                'app_create_walk_prologue[conceptOfDay]' => 'conceptOfDay',
            ]
        );
        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        $form = $crawler->filter('[data-test="save-and-finish-way-point"]')->form();
        $form->setValues(
            [
                'app_create_way_point[locationName]' => 'location',
                'app_create_way_point[note]' => 'note',
            ]
        );
        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        $form = $crawler->filter('[data-test="create-walk"]')->form();
        $form->setValues(
            [
                'app_create_walk[walkReflection]' => 'walkReflectionText',
                'app_create_walk[systemicAnswer]' => 'systemicAnswerText',
            ]
        );
        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        $this->assertContains(
            'Wegpunkt wurde erfolgreich erstellt.',
            $crawler->text()
        );
        $this->assertContains(
            'Runde wurde erfolgreich erstellt.',
            $crawler->text()
        );
    }
}
