<?php
namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WalkExportTest extends WebTestCase
{
    public function testWalkExportIsSuccessful()
    {
        $client = static::makeClient(true);

        ob_start();
        $client->request('GET', '/walkexport');
        ob_get_clean();

        $this->isSuccessful($client->getResponse());
    }
}
