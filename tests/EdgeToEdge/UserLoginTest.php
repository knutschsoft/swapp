<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

class UserLoginTest extends BaseWebTestCase
{
    private const USERNAME_ADMIN = 'admin';

    private const PASSWORD_ADMIN = 'admin';

    private const DISABLED_NAME = 'inactiveuser';

    private const BAD_NAME = 'ashd73ddb';

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
