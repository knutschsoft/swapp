<?php
declare(strict_types=1);

namespace Tests\AppBundle\EdgeToEdge;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class UserLoginTest extends WebTestCase
{
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const USERNAME_ADMIN = 'admin';
    const PASSWORD_ADMIN = 'admin';
    const DISABLED_NAME = 'inactiveuser';
    const DISABLED_PASS = 'inactiveuser';
    const BAD_NAME = 'ashd73ddb';
    const BAD_PASS = 'www';

    public function testUserLoginBackendFailsWithWrongRole(): void
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

    public function testUserLoginBackend(): void
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

    public function testUserLoginFrontend(): void
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

    /**
     * @param string $url
     *
     * @dataProvider urlProvider
     */
    public function testUserLoginFrontendWithBadCredentialsIsRedirected(string $url): void
    {
        $this->loadUserFixtures();

        $client = static::makeClient([
            'username' => self::BAD_NAME,
            'password' => self::BAD_PASS,
        ]);

        $client->request('GET', $url);
        $this->assertStatusCode(302, $client);
    }
    /**
     * @param string $url
     *
     * @dataProvider urlProvider
     */
    public function testUserLoginFrontendDisabledAccountIsRedirected(string $url): void
    {
        $this->loadUserFixtures();

        $client = static::makeClient([
            'username' => self::DISABLED_NAME,
            'password' => self::DISABLED_PASS,
        ]);

        $client->request('GET', $url);
        $this->assertStatusCode(302, $client);
        $client->followRedirect();
        $this->assertContains('/login', $client->getCrawler()->getUri());
    }

    /**
     * @param string $url
     *
     * @dataProvider urlProvider
     */
    public function testUserLoginFrontendAdminUserHasAccess(string $url): void
    {
        $this->loadUserFixtures();

        $client = static::makeClient([
            'username' => self::USERNAME_ADMIN,
            'password' => self::PASSWORD_ADMIN,
        ]);

        $crawler = $client->request('GET', $url);
        $this->isSuccessful($client->getResponse(), true);

        $this->assertStatusCode(200, $client);
    }

    public function urlProvider()
    {
        return array(
            array('/walks'),
            array('/tag'),
            array('/createtag'),
            array('/eadmin/?action=list&entity=Team'),
            array('/eadmin/?action=list&entity=Walk'),
            array('/eadmin/?action=list&entity=WayPoint'),
            array('/eadmin/?action=list&entity=Tag'),
            array('/eadmin/?action=list&entity=User'),
            array('/eadmin/?action=list&entity=Guest'),
            array('/eadmin/?action=list&entity=SystemicQuestion'),
            // ...
        );
    }
    private function loadUserFixtures(): void
    {
        $this->loadFixtureFiles([
            '@AppBundle/DataFixtures/ORM/test/user.yml',
        ]);
    }
}
