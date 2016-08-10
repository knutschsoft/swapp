<?php
namespace AppBundle\Controller;

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
}
