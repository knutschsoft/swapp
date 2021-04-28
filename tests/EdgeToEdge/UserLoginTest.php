<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

class UserLoginTest extends BaseWebTestCase
{
    private const USERNAME = 'username';

    private const PASSWORD = 'password';

    private const USERNAME_ADMIN = 'admin';

    private const PASSWORD_ADMIN = 'admin';

    private const DISABLED_NAME = 'inactiveuser';

    private const BAD_NAME = 'ashd73ddb';

    public function testUserLoginBackendFailsWithWrongRole(): void
    {
        $this->loadUserFixtures();

        $client = $this->getClient();

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
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(1 === $crawler->selectButton('Anmelden')->count(), 'Button "Anmelden" not found');
    }

    public function testUserLoginBackend(): void
    {
        $client = $this->getClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/eadmin');

        $form = $crawler->selectButton('Anmelden')->form(
            [
                '_username' => self::USERNAME_ADMIN,
                '_password' => self::PASSWORD_ADMIN,
                '_remember_me' => 'on',
            ]
        );

        $crawler = $client->submit($form);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(0 === $crawler->selectButton('Anmelden')->count(), 'Button "Anmelden" found');
    }

    public function testUserLoginFrontend(): void
    {
        $this->loadUserFixtures();

        $client = $this->getClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Anmelden')->form(
            [
                '_username' => self::USERNAME_ADMIN,
                '_password' => self::PASSWORD_ADMIN,
                '_remember_me' => 'on',
            ]
        );

        $crawler = $client->submit($form);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(0 === $crawler->filter('Anmelden')->count(), 'Button "Anmelden" found');
    }

    /**
     * @dataProvider urlProvider
     */
    public function testUserLoginFrontendWithBadCredentialsIsRedirected(string $url): void
    {
        $this->loadUserFixtures();

        $user = self::BAD_NAME;
        $client = $this->getClient($user);

        $client->request('GET', $url);
        $client->followRedirect();
        $this->assertStringContainsString('/login', $client->getCrawler()->getUri());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testUserLoginFrontendDisabledAccountIsRedirected(string $url): void
    {
        $this->loadUserFixtures();

        $user = self::DISABLED_NAME;
        $client = $this->getClient($user);

        $client->request('GET', $url);
        $client->followRedirect();
        $this->assertStringContainsString('/login', $client->getCrawler()->getUri());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testUserLoginFrontendAdminUserHasAccess(string $url): void
    {
        $this->loadUserFixtures();

        $user = self::USERNAME_ADMIN;
        $client = $this->getClient($user);

        $crawler = $client->request('GET', $url);
        $this->assertStatusCode(200, $client, $crawler, $url, $user);
    }

    public function urlProvider(): array
    {
        return [
            ['/walks'],
            ['/tag'],
            ['/createtag'],
            ['/eadmin/?action=list&entity=Team'],
            ['/eadmin/?action=list&entity=Walk'],
            ['/eadmin/?action=list&entity=WayPoint'],
            ['/eadmin/?action=list&entity=Tag'],
            ['/eadmin/?action=list&entity=User'],
            ['/eadmin/?action=list&entity=Guest'],
            ['/eadmin/?action=list&entity=SystemicQuestion'],
            // ...
        ];
    }

    private function loadUserFixtures(): void
    {
        $this->loadFixtureFiles([
            'fixtures/test/user.yml',
        ]);
    }
}
