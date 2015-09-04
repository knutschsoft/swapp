<?php
namespace AppBundle\Tests\EdgeToEdge;

class ImageUploadTest extends BaseWebTestCase
{
    public function testImageUpload()
    {
        $this->logIn();
        $this->client->followRedirects(true);
        $crawler = $this->client->request('GET', '/walks');

        $crawler = $crawler->selectLink('Runde beginnen');


        $crawler = $this->client->click($crawler->link());

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code of "Runde beginnen" is ' . $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('Wegpunkt anlegen')->form(
            [
                'app_create_walk_prologue[name]' => 'name test',
                'app_create_walk_prologue[conceptOfDay]' => 'conceptOfDay test',
            ]
        );

        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code of "Wegpunkt anlegen" is ' . $this->client->getResponse()->getStatusCode()
        );

//        $crawler = $this->client->followRedirect();

        // submit invalid form; we are redirected to this form
        $form = $crawler->selectButton('speichern')->form();
        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code of "Wegpunkt anlegen" is ' . $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('speichern')->form();


//        $crawler = $this->client->followRedirect();

        $fileLocation = $this->client->getContainer()->getParameter('kernel.root_dir');
        $fileLocation .= '/../src/AppBundle/Tests/fixtures/image.jpg';

        $form['app_create_way_point[imageFile][file]']->upload($fileLocation);
        $form['app_create_way_point[locationName]'] = 'Buxtehude is the locationName value';
        $form['app_create_way_point[note]'] = 'note value';

        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code of "Wegpunkt anlegen" is ' . $this->client->getResponse()->getStatusCode()
        );

        // check for saved image
        $link = $crawler->selectLink('Wegpunkt ansehen');
        $crawler = $this->client->click($link->link());
        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code of "Wegpunkt anlegen" is ' . $this->client->getResponse()->getStatusCode()
        );

        $img = $crawler->filter('img');
        $this->assertSame('/images/way_points/image.jpg', $img->attr('src'));
    }
}
