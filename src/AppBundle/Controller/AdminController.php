<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController
{
    /**
     * @return User
     */
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }


    /**
     * @return Team
     */
    public function createNewTeamEntity()
    {
        return new Team();
    }

    /**
     * @param User $user
     */
    public function prePersistUserEntity(User $user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    /**
     * @param User $user
     */
    public function preUpdateUserEntity(User $user)
    {
        $teams = $user->getTeams();
        if ($teams) {
            $user->setTeams($teams);
        }
        $this->get('fos_user.user_manager')->updateUser($user, false);
    }

    /**
     * @param Team $team
     */
    public function prePersistTeamEntity(Team $team)
    {
        $users = $team->getUsers();
        if($users) {
            foreach ($users as $user) {
                $user->addTeam($team);
            }
        }
    }

    /**
     * @param Team $team
     */
    public function preUpdateTeamEntity(Team $team)
    {
        $users = $team->getUsers();
        if ($users) {
            $team->setUsers($users);
        }
    }
}
