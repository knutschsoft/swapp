<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController
{
    public function createNewUserEntity(): User
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function createNewTeamEntity(): Team
    {
        return new Team();
    }

    public function prePersistUserEntity(User $user): void
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    public function preUpdateUserEntity(User $user): void
    {
        $teams = $user->getTeams();
        $user->setTeams($teams);
        $this->get('fos_user.user_manager')->updateUser($user, false);
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
}
