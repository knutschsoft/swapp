<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class WalkExportTest extends WebTestCase
{
    public function testWalkExportIsSuccessful(): void
    {
        $credentials = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $client = static::makeClient($credentials);

        \ob_start();
        $client->request('GET', '/walkexport');
        \ob_get_clean();

        $this->isSuccessful($client->getResponse());
    }
}
