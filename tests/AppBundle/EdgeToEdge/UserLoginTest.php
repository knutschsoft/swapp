<?php
namespace Tests\AppBundle\EdgeToEdge;

class UserLoginTest extends BaseWebTestCase
{
    public function testUserLoginBackendFailsWithWrongRole()
    {
        $this->client->followRedirects(true);
        $crawler = $this->client->request('GET', '/eadmin');

        $form = $crawler->selectButton('Anmelden')->form(array(
            '_username' => self::USERNAME,
            '_password' => self::PASSWORD,
            '_remember_me' => 'on',
        ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(
            403,
            $this->client->getResponse()->getStatusCode(),
            'Response have wrong status code'
        );
//        $this->assertTrue($crawler->selectButton('Anmelden')->count() === 1, 'Button "Anmelden" not found');
    }

    public function testUserLoginBackend()
    {
        $this->user->setRoles(array('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER'));
        $this->user->setSuperAdmin(true);
        $this->userManager->updateUser($this->user);

        $this->userManager->refreshUser($this->user);

        $this->client->followRedirects(true);
        $crawler = $this->client->request('GET', '/eadmin');

        $form = $crawler->selectButton('Anmelden')->form(array(
            '_username' => self::USERNAME,
            '_password' => self::PASSWORD,
            '_remember_me' => 'on',
        ));

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response not successful');
        $this->assertTrue($crawler->selectButton('Anmelden')->count() === 0, 'Button "Anmelden" found');
    }

    public function testUserLoginFrontend()
    {
        $this->client->followRedirects(true);
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Anmelden')->form(array(
            '_username' => self::USERNAME,
            '_password' => self::PASSWORD,
            '_remember_me' => 'on',
        ));

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'Response not successful');
        $this->assertTrue($crawler->filter('Anmelden')->count() === 0, 'Button "Anmelden" found');
    }
}
