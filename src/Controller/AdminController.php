<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Team;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends EasyAdminController
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function createNewUserEntity(): User
    {
        return User::createEmpty();
    }

    public function createNewTeamEntity(): Team
    {
        return new Team();
    }

    public function persistUserEntity(User $user): void
    {
        $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));

        parent::persistEntity($user);
    }

    public function updateUserEntity(User $user): void
    {
        if ($user->getPlainPassword()) {
            $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));
        }

        parent::updateEntity($user);
    }

    public function persistTeamEntity(Team $team): void
    {
        $users = $team->getUsers();
        foreach ($users as $user) {
            $user->addTeam($team);
        }

        parent::persistEntity($team);
    }
}
