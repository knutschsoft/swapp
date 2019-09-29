<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepositoryInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Webmozart\Assert\Assert;

class AdminController extends BaseAdminController
{
    public function createNewUserEntity(): User
    {
        return new User();
    }

    public function createNewTeamEntity(): Team
    {
        return new Team();
    }

    public function prePersistUserEntity(User $user): void
    {
        $this->getUserRepository()->save($user);
    }

    public function preUpdateUserEntity(User $user): void
    {
        $teams = $user->getTeams();
        $user->setTeams($teams);
        $this->getUserRepository()->save($user);
    }

    public function prePersistTeamEntity(Team $team): void
    {
        $users = $team->getUsers();
        foreach ($users as $user) {
            $user->addTeam($team);
        }
    }

    public function preUpdateTeamEntity(Team $team): void
    {
        $users = $team->getUsers();
        $team->setUsers($users);
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $userRepository = $this->get(UserRepositoryInterface::class);
        Assert::isInstanceOf($userRepository, UserRepositoryInterface::class);

        return $userRepository;
    }
}
