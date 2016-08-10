<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController
{
    /**
     * @return User
     */
    public function createNewUserEntity()
    {
        return  new User();
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
        $teams = $user->getTeams();
        if($teams) {
            $user->setTeams($teams);
            foreach ($teams as $team) {
                $team->addUser($user);
            }
        }
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
}
