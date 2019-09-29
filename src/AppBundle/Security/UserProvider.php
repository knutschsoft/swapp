<?php
declare(strict_types=1);

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($usernameOrEmail): UserInterface
    {
        $user = $this->userRepository->loadUserByUsername($usernameOrEmail);
        if (\is_null($user)) {
            throw new UsernameNotFoundException();
        }

        if ($user instanceof User && !$user->isEnabled()) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the user is not supported
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                \sprintf('Expected an instance of %s, but got "%s".', User::class, \get_class($user))
            );
        }

        $reloadedUser = $this->userRepository->findOneBy(['id' => $user->getId()]);
        if (null === $reloadedUser) {
            throw new UsernameNotFoundException(\sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
