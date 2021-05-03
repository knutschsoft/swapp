<?php
declare(strict_types=1);

namespace App\Tests\Context;

use App\Entity\Team;
use App\Entity\User;
use App\Entity\Walk;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\WalkRepository;
use App\Value\AgeRange;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert as Assertion;

trait RepositoryTrait
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private TeamRepository $teamRepository;
    private WalkRepository $walkRepository;

    public function initRepositories(KernelInterface $kernel): void
    {
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assertion::notNull($serviceContainer);
        Assertion::isInstanceOf($serviceContainer, Container::class);

        $userRepository = $serviceContainer->get(UserRepository::class);
        \assert($userRepository instanceof UserRepository);
        $this->userRepository = $userRepository;
        $teamRepository = $serviceContainer->get(TeamRepository::class);
        \assert($teamRepository instanceof TeamRepository);
        $this->teamRepository = $teamRepository;
        $walkRepository = $serviceContainer->get(WalkRepository::class);
        \assert($walkRepository instanceof WalkRepository);
        $this->walkRepository = $walkRepository;

        $em = $serviceContainer->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);
        $this->em = $em;
    }

    protected function getUserByEmail(string $email): User
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);
        Assertion::notNull($user, \sprintf('User with email "%s" not found.', \trim($email)));

        return $user;
    }

    protected function getTeamByName(string $name): Team
    {
        $team = $this->teamRepository->findOneBy(['name' => $name]);
        Assertion::notNull($team, \sprintf('Team with name "%s" not found.', \trim($name)));

        return $team;
    }

    protected function getWalkByName(string $name): Walk
    {
        $walk = $this->walkRepository->findOneBy(['name' => $name]);
        Assertion::notNull($walk, \sprintf('Walk with name "%s" not found.', \trim($name)));

        return $walk;
    }

    /**
     * @param string $usersString
     *
     * @return User[]
     */
    protected function getUsersFromString(string $usersString): array
    {
        $users = [];
        if (!$usersString) {
            return $users;
        }
        $userStrings = \explode(',', $usersString);
        foreach ($userStrings as $userString) {
            $users[] = $this->getUserByEmail($userString);
        }

        return $users;
    }

    /**
     * @param string $ageRangesString
     *
     * @return AgeRange[]
     */
    protected function getAgeRangesFromString(string $ageRangesString): array
    {
        $ageRangeStrings = \explode(',', $ageRangesString);
        $ageRanges = [];

        foreach ($ageRangeStrings as $ageRangeString) {
            $ageRanges[] = AgeRange::fromString($ageRangeString);
        }

        return $ageRanges;
    }
}
