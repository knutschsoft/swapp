<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

class UserLoginTest extends BaseWebTestCase
{
    private const USERNAME_ADMIN = 'admin';

    private const PASSWORD_ADMIN = 'admin';

    private const DISABLED_NAME = 'inactiveuser';

    private const BAD_NAME = 'ashd73ddb';

    public function testUserLoginFrontend(): void
    {
        $this->loadUserFixtures();

        $client = $this->getClient();
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

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertTrue(0 === $crawler->filter('Anmelden')->count(), 'Button "Anmelden" found');
    }

    /**
     * @param string $url
     *
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
     * @param string $url
     *
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
     * @param string $url
     *
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
        return array(
            array('/walks'),
            array('/tag'),
            array('/createtag'),
            // ...
        );
    }

    private function loadUserFixtures(): void
    {
        $this->loadFixtureFiles([
            'fixtures/test/user.yml',
        ]);
    }
}
