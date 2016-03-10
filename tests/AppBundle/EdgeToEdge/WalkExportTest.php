<?php
namespace Tests\AppBundle\EdgeToEdge;

class WalkExportTest extends BaseWebTestCase
{
    public function testWalkExportIsSuccessful()
    {
        $this->logIn();
        ob_start();
        $this->client->request('GET', '/walkexport');
        ob_get_clean();

        $this->assertTrue(
            $this->client->getResponse()->isSuccessful(),
            'status code is ' . $this->client->getResponse()->getStatusCode()
        );
    }
}
