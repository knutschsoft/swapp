<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Webmozart\Assert\Assert;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request): bool
    {
        return 'login' === $request->attributes->get('_route')
            && $request->isMethod('POST')
            && ($request->request->has('_username') || $request->request->has('_password'));
    }

    public function getCredentials(Request $request): array
    {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $session = $request->getSession();
        Assert::isInstanceOf($session, SessionInterface::class);
        $session->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        Assert::isArray($credentials);
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $userProvider->loadUserByUsername($credentials['username']);
        Assert::isInstanceOf($user, User::class);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        Assert::isArray($credentials);
        /** @var User $appUser */
        $appUser = $user;

        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']) && $appUser->isEnabled();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        $session = $request->getSession();
        Assert::isInstanceOf($session, SessionInterface::class);

        $user = $token->getUser();
        if ($user instanceof User) {
            $user->updateLastLoginAt();
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        $targetPath = $this->getTargetPath($session, $providerKey);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        $targetPath = $request->request->get('_target_path');
        if (\is_string($targetPath)) {
            return new RedirectResponse($targetPath);
        }

        $url = $this->urlGenerator->generate('home');
        Assert::notNull($url);

        return new RedirectResponse($url);
    }

    protected function getLoginUrl(): string
    {
        $url = $this->urlGenerator->generate('login');
        Assert::notNull($url);

        return $url;
    }
}
