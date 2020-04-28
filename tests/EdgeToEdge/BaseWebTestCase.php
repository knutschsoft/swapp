<?php
declare(strict_types=1);

namespace App\Tests\EdgeToEdge;

use App\Security\UserProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class BaseWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    private LoaderInterface $loader;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    protected function purgeDatabase(): void
    {
        $purger = new ORMPurger($this->getDoctrineManager());
        $purger->purge();
    }

    protected function loadFixtureFiles(array $files): array
    {
        return $this->getLoader()->load($files);
    }

    protected function loadAllFixtureFiles(): array
    {
        $this->createClient();

        return $this->loadFixtureFiles(
            [
                'fixtures/Article.yml',
                'fixtures/ArticleName.yml',
                'fixtures/ArticleCategory.yml',
                'fixtures/MigrationLog.yml',
                'fixtures/User.yml',
                'fixtures/WikiEnvironment.yml',
            ]
        );
    }

    protected function getDoctrineManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    protected function assertStatusCode(
        int $expectedStatusCode,
        KernelBrowser $client,
        Crawler $crawler,
        string $url,
        string $user = ''
    ): void {
        $this->assertEquals(
            $expectedStatusCode,
            $client->getResponse()->getStatusCode(),
            \sprintf(
                'response not successful; url is "%s", status code is "%s", user is "%s" and h1 is "%s".%s',
                $url,
                $client->getResponse()->getStatusCode(),
                $user,
                $crawler->filter('h1.exception-message')->count() ? \trim(
                    $crawler->filter('h1.exception-message')->text()
                ) : 'empty',
                302 === $client->getResponse()->getStatusCode() ?
                    ' It wants to redirect to '.$client->followRedirect()->getUri()
                    : ''
            )
        );
    }

    protected static function getClient(string $user = 'anonymous'): KernelBrowser
    {
        return self::createAppClient($user);
    }

    private static function createAppClient(string $username): KernelBrowser
    {
        $client = static::createClient(['environment' => 'test'], []);

        if ('anonymous' !== $username) {
            $session = self::$container->get('session');
            $firewallName = 'main';

            try {
                $user = self::$container
                    ->get(UserProvider::class)
                    ->loadUserByUsername($username);
                \assert($user instanceof UserInterface || null === $user);
            } catch (UsernameNotFoundException $e) {
                $user = null;
            }

            if (!\is_null($user)) {
                $token = new PostAuthenticationGuardToken($user, $firewallName, $user->getRoles());
                $session->set('_security_'.$firewallName, \serialize($token));
                $session->save();

                $cookie = new Cookie($session->getName(), $session->getId());
                $client->getCookieJar()->set($cookie);
            }
        }

        return $client;
    }

    private function getLoader(): LoaderInterface
    {
        if (empty($this->loader)) {
            self::bootKernel();
            $this->loader = self::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        }

        return $this->loader;
    }
}
