<?php
declare(strict_types=1);

namespace App\Tests\Context;

use App\Entity\Client;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Walk;
use App\Entity\WayPoint;
use App\Repository\ClientRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Repository\WalkRepository;
use App\Repository\WayPointRepository;
use App\Value\AgeRange;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert as Assertion;

trait RepositoryTrait
{
    private EntityManagerInterface $em;
    private ClientRepository $clientRepository;
    private UserRepository $userRepository;
    private TeamRepository $teamRepository;
    private WalkRepository $walkRepository;
    private WayPointRepository $wayPointRepository;

    public function initRepositories(KernelInterface $kernel): void
    {
        $serviceContainer = $kernel->getContainer()->get('test.service_container');
        Assertion::notNull($serviceContainer);
        Assertion::isInstanceOf($serviceContainer, Container::class);

        $clientRepository = $serviceContainer->get(ClientRepository::class);
        \assert($clientRepository instanceof ClientRepository);
        $this->clientRepository = $clientRepository;
        $userRepository = $serviceContainer->get(UserRepository::class);
        \assert($userRepository instanceof UserRepository);
        $this->userRepository = $userRepository;
        $teamRepository = $serviceContainer->get(TeamRepository::class);
        \assert($teamRepository instanceof TeamRepository);
        $this->teamRepository = $teamRepository;
        $walkRepository = $serviceContainer->get(WalkRepository::class);
        \assert($walkRepository instanceof WalkRepository);
        $this->walkRepository = $walkRepository;
        $wayPointRepository = $serviceContainer->get(WayPointRepository::class);
        \assert($wayPointRepository instanceof WayPointRepository);
        $this->wayPointRepository = $wayPointRepository;

        $em = $serviceContainer->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);
        $this->em = $em;
    }

    protected function getClientByEmail(string $email): Client
    {
        return $this->clientRepository->findOneByEmail($email);
    }

    protected function getUserByEmail(string $email): User
    {
        $email = \trim($email);
        $user = $this->userRepository->findOneBy(['email' => $email]);
        Assertion::notNull($user, \sprintf('User with email "%s" not found.', $email));

        return $user;
    }

    protected function getTeamByName(string $name): Team
    {
        $name = \trim($name);
        $team = $this->teamRepository->findOneBy(['name' => $name]);
        Assertion::notNull($team, \sprintf('Team with name "%s" not found.', $name));

        return $team;
    }

    protected function getWalkByName(string $name): Walk
    {
        $name = \trim($name);
        $walk = $this->walkRepository->findOneBy(['name' => $name]);
        Assertion::notNull($walk, \sprintf('Walk with name "%s" not found.', $name));

        return $walk;
    }

    protected function getWayPointByLocationName(string $locationName): WayPoint
    {
        $locationName = \trim($locationName);
        $wayPoint = $this->wayPointRepository->findOneBy(['locationName' => $locationName]);
        Assertion::notNull($wayPoint, \sprintf('WayPoint with locationName "%s" not found.', $locationName));

        return $wayPoint;
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
        if (!$ageRangesString) {
            return $ageRanges;
        }

        foreach ($ageRangeStrings as $ageRangeString) {
            $ageRanges[] = AgeRange::fromString($ageRangeString);
        }

        return $ageRanges;
    }
}
