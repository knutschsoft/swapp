<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTeamData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_TEAMS = 4;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $names = [
            'Team Rocket',
            'ESL Players',
            'Team Rosa Panda',
            'Traktorfreaks',
        ];

        foreach ($names as $key => $name) {
            $team = new Team();
            $team->setName($name);
            $team->setUsers($this->getUsersReferences());

            $manager->persist($team);
            $manager->flush();

            $this->setReference('team-' . ($key + 1), $team);
        }
    }

    /**
     * @return User[]
     */
    private function getUsersReferences()
    {
        $users = [];
        $userIds = [];
        for ($i = 0; $i < LoadUserData::NUM_USERS; $i++) {
            $userId = rand(1, LoadUserData::NUM_USERS);
            if (in_array($userId, $userIds)) {

                break;
            }
            $userIds[] = $userId;

            $users[] = $this->getReference('user-' . $userId);
        }

        return $users;
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 3; // load after LoadUserData
    }
}
