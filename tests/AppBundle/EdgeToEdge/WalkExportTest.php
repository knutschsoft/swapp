<?php
namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WalkExportTest extends WebTestCase
{
    public function testWalkExportIsSuccessful()
    {
        $credentials = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $client = static::makeClient($credentials);

        ob_start();
        $client->request('GET', '/walkexport');
        ob_get_clean();

        $this->isSuccessful($client->getResponse());
    }
}
