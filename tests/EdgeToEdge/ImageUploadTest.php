<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadTest extends BaseWebTestCase
{
    public function testNavigateToCreateWaypointForm(): array
    {
        $this->loadAllFixtures();
        $client = $this->createAuthenticatedClient();

        $crawler = $client->request('GET', '/walks');
        $crawler = $crawler->selectLink('Runde beginnen');
        $crawler = $client->click($crawler->link());
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Wegpunkt anlegen')->form(
            [
                'walk_prologue[name]' => 'name test',
                'walk_prologue[conceptOfDay]' => 'conceptOfDay test',
                'walk_prologue[weather]' => 'Sonne',
            ]
        );

        $crawler = $client->submit($form);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Runde wurde erfolgreich gestartet.', $crawler->text());

        return [$client, $crawler];
    }

    /**
     * @depends testNavigateToCreateWaypointForm
     */
    public function testSubmittingEmptyFormRedirectsToForm(array $args): void
    {
        $client = $args[0];
        \assert($client instanceof KernelBrowser);
        $crawler = $args[1];
        \assert($crawler instanceof Crawler);

        $form = $crawler->selectButton('speichern')->form();
        $crawler = $client->submit($form);
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('speichern')->form();
        $this->assertArrayHasKey(
            'way_point',
            $form->getPhpValues(),
            'Form didnt not redirect to itself when submitting with invalid values'
        );
    }

    /**
     * @depends testNavigateToCreateWaypointForm
     */
    public function testSubmittingValidImageSavesImage(array $args): void
    {
        $client = $args[0];
        \assert($client instanceof KernelBrowser);
        $crawler = $args[1];
        \assert($crawler instanceof Crawler);

        $form = $crawler->selectButton('speichern')->form();
        $fileLocation = $client->getKernel()->getProjectDir();
        $fileLocation .= '/tests/fixtures/image.jpg';

        $form['way_point[imageFile][file]']->upload($fileLocation);
        $form['way_point[locationName]'] = 'Buxtehude is the locationName value';
        $form['way_point[note]'] = 'note value';

        $crawler = $client->submit($form);
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // check for saved image - todo refactor to own testmethod
        $link = $crawler->selectLink('Wegpunkt');
        $crawler = $client->click($link->link());
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $img = $crawler->filter('img');
        $this->assertSame('/images/way_points/image.jpg', $img->attr('src'));
    }

    /**
     * @depends testNavigateToCreateWaypointForm
     */
    public function testUploadingImageExcceedingMaxFileSizeRendersFormErrors(array $args): void
    {
        $client = $args[0];
        \assert($client instanceof KernelBrowser);
        $crawler = $args[1];
        \assert($crawler instanceof Crawler);

        $fileName = 'test_image_42MB.png';
        $fileLocation = $client->getKernel()->getProjectDir();
        $fileLocation .= '/tests/fixtures/'.$fileName;
        $imgStub = new UploadedFile($fileLocation, $fileName);

        $form = $crawler->selectButton('speichern')->form();
        $form['way_point[imageFile][file]'] = $imgStub;
        $form['way_point[locationName]'] = 'Buxtehude is the locationName value';
        $form['way_point[note]'] = 'note value';
        $crawler = $client->submit($form);

        // only working with german translation messages
        $text = $crawler->text();
        $this->assertStringContainsString(
            'Datei ist zu groß',
            $text,
            'Error uploading '.$fileName.'. Either upload_max_filesize in php.ini is lower than the assertion.
                or file was uploaded without triggering a form error.'
        );
    }

    /** @return \Doctrine\Common\DataFixtures\Executor\AbstractExecutor|void|null */
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

    private function createAuthenticatedClient(): KernelBrowser
    {
        $credentials = [
            'username' => 'waldi_beta',
            'password' => 'waldi_beta',
        ];

        $client = $this->getClient($credentials['username']);
        $client->followRedirects(true);

        return $client;
    }
}
