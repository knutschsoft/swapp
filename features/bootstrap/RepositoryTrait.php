<?php
declare(strict_types=1);

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

trait RepositoryTrait
{
    private UserRepository $userRepository;

    public function initRepositories(KernelInterface $kernel): void
    {
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assert::notNull($serviceContainer);
        Assert::isInstanceOf($serviceContainer, Container::class);

        $this->userRepository = $serviceContainer->get(UserRepository::class);
    }

    protected function getUserByEmail(string $email): User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }
}
