<?php
namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class UserLoginTest extends WebTestCase
{
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const USERNAME_ADMIN = 'admin';
    const PASSWORD_ADMIN = 'admin';

    public function testUserLoginBackendFailsWithWrongRole()
    {
        $client = static::makeClient();

        $client->followRedirects(true);
        $crawler = $client->request('GET', '/eadmin');

        $form = $crawler->selectButton('Anmelden')->form(
            [
                '_username' => self::USERNAME,
                '_password' => self::PASSWORD,
                '_remember_me' => 'on',
            ]
        );

        $crawler = $client->submit($form);
        $this->isSuccessful($client->getResponse());
        $this->assertTrue($crawler->selectButton('Anmelden')->count() === 1, 'Button "Anmelden" not found');
    }

    public function testUserLoginBackend()
    {
        $client = static::makeClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/eadmin');

        $form = $crawler->selectButton('Anmelden')->form(
            array(
                '_username' => self::USERNAME_ADMIN,
                '_password' => self::PASSWORD_ADMIN,
                '_remember_me' => 'on',
            )
        );

        $crawler = $client->submit($form);

        $this->isSuccessful($client->getResponse());
        $this->assertTrue($crawler->selectButton('Anmelden')->count() === 0, 'Button "Anmelden" found');
    }

    public function testUserLoginFrontend()
    {
        $client = static::makeClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Anmelden')->form(
            array(
                '_username' => self::USERNAME_ADMIN,
                '_password' => self::PASSWORD_ADMIN,
                '_remember_me' => 'on',
            )
        );

        $crawler = $client->submit($form);

        $this->isSuccessful($client->getResponse());
        $this->assertTrue($crawler->filter('Anmelden')->count() === 0, 'Button "Anmelden" found');
    }
}
