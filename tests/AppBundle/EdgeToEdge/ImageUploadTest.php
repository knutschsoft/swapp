<?php
namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class ImageUploadTest extends WebTestCase
{
    public function testImageUpload()
    {
        $this->loadFixtureFiles(
            [
                '@AppBundle/DataFixtures/ORM/tag.yml',
                '@AppBundle/DataFixtures/ORM/guest.yml',
                '@AppBundle/DataFixtures/ORM/team.yml',
                '@AppBundle/DataFixtures/ORM/user.yml',
                '@AppBundle/DataFixtures/ORM/walk.yml',
                '@AppBundle/DataFixtures/ORM/systemicQuestion.yml',
                '@AppBundle/DataFixtures/ORM/wayPoint.yml',
            ]
        );

        $credentials = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $client = static::makeClient($credentials);
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/walks');

        $crawler = $crawler->selectLink('Runde beginnen');
        $crawler = $client->click($crawler->link());
        $this->isSuccessful($client->getResponse());

        $form = $crawler->selectButton('Wegpunkt anlegen')->form(
            [
                'app_create_walk_prologue[name]' => 'name test',
                'app_create_walk_prologue[conceptOfDay]' => 'conceptOfDay test',
            ]
        );

        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        // submit invalid form; we are redirected to this form
        $form = $crawler->selectButton('speichern')->form();
        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        $form = $crawler->selectButton('speichern')->form();

        $fileLocation = $client->getContainer()->getParameter('kernel.root_dir');
        $fileLocation .= '/../tests/AppBundle/fixtures/image.jpg';

        $form['app_create_way_point[imageFile][file]']->upload($fileLocation);
        $form['app_create_way_point[locationName]'] = 'Buxtehude is the locationName value';
        $form['app_create_way_point[note]'] = 'note value';

        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());

        // check for saved image
        $link = $crawler->selectLink('Wegpunkt ansehen');
        $crawler = $client->click($link->link());
        $this->isSuccessful($client->getResponse());

        $img = $crawler->filter('img');
        $this->assertSame('/images/way_points/image.jpg', $img->attr('src'));
    }
}
