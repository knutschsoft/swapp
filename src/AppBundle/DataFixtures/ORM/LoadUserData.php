<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_USERS = 5;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $names = [
            'Karl',
            'Gustav',
            'Luise',
            'Bob',
            'admin',
        ];

        $roles = [
            ['ROLE_USER', 'ROLE_ADMIN'],
            ['ROLE_ADMIN'],
            ['ROLE_USER'],
        ];

        foreach ($names as $key => $name) {
            $user = new User();
            $user->setUsername($name);
            $user->setPlainPassword($name);

            $role = $name === 'admin' ? ['ROLE_ADMIN'] : $roles[array_rand($roles, 1)];
            $user->setRoles($role);
            $user->setEnabled(true);
            $user->setEmail(mb_strtolower($name) . '@gmx.de');
            $user->setWalks($this->getWalksReferences());
            $user->setTeams($this->getTeamsReferences());

            $manager->persist($user);
            $manager->flush();

            $this->addReference('user-' . ($key + 1), $user);
        }
    }

    private function getTeamsReferences()
    {
        $teams = [];
        for ($i = 0; $i < rand(1, 10); $i++) {
//            $teams[] = $this->getReference('team-' . rand(1, LoadTeamData::NUM_TEAMS));
        }

        return $teams;
    }

    /**
     * @return Walk[]
     */
    private function getWalksReferences()
    {
        $walks = [];
        for ($i = 0; $i < rand(1, 10); $i++) {
//            $walks[] = $this->getReference('walk-' . rand(1, LoadWalkData::NUM_WALKS));
        }

        return $walks;
    }


    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 3; // load after LoadWalkData
    }
}
