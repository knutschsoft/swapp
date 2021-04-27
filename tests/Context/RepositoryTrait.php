<?php
declare(strict_types=1);

namespace App\Tests\Context;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert as Assertion;

trait RepositoryTrait
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function initRepositories(KernelInterface $kernel): void
    {
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assertion::notNull($serviceContainer);
        Assertion::isInstanceOf($serviceContainer, Container::class);

        $this->userRepository = $serviceContainer->get(UserRepository::class);
        $this->em = $serviceContainer->get('doctrine.orm.entity_manager');
    }

    protected function getUserByEmail(string $email): User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        Assertion::notNull($user, \sprintf('User with email "%s" not found.', \trim($email)));

        return $user;
    }
}
