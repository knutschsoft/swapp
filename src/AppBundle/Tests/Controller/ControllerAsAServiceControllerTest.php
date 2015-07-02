<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerAsAServiceControllerTest extends WebTestCase
{
    public function testExample()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/caas');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Congratulations")')->count() > 0);
    }

    public function testParamConverterExample()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/pc/a/b');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Congratulations")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("stdClass")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("another stdClass")')->count() > 0);
    }
}
