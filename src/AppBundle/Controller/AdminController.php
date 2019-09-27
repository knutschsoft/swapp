<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\UserInterface;
use Webmozart\Assert\Assert;

class AdminController extends BaseAdminController
{
    public function createNewUserEntity(): UserInterface
    {
        return $this->getUserManager()->createUser();
    }

    public function createNewTeamEntity(): Team
    {
        return new Team();
    }

    public function prePersistUserEntity(User $user): void
    {
        $this->getUserManager()->updateUser($user, false);
    }

    public function preUpdateUserEntity(User $user): void
    {
        $teams = $user->getTeams();
        $user->setTeams($teams);
        $this->getUserManager()->updateUser($user, false);
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

    private function getUserManager(): UserManager
    {
        $userManager = $this->get('fos_user.user_manager');
        Assert::isInstanceOf($userManager, UserManager::class);
        
        return $userManager;
    }
}
