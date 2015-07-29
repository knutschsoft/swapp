<?php
namespace AppBundle\Tests\EdgeToEdge;

use AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class BaseWebTestCase extends WebTestCase
{
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const EMAIL = 'email';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var UserManager
     */
    protected $userManager;

    public function setup()
    {
        $this->client = $this->createClient();
        $this->userManager = $this->client->getContainer()->get('fos_user.user_manager');

        $user = $this->userManager->findUserByUsername(self::USERNAME);
        if (!$user) {
            $user = new User();
            $user->setUsername(self::USERNAME);
            $user->setPlainPassword(self::PASSWORD);
            $user->setEmail(self::EMAIL);
            $user->setEnabled(true);

            $this->userManager->updateUser($user);
        }

        $this->user = $user;
    }

    public function tearDown()
    {
        $user = $this->userManager->findUserByUsername(self::USERNAME);
        $this->userManager->deleteUser($user);
    }

    public function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken($this->user, null, $firewall, array('ROLE_USER', 'ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
