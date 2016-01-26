<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Team;
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
            'Team Nord',
            'Team Süd',
        ];

        foreach ($names as $key => $name) {
            $team = new Team();
            $team->setName($name);
            if ($name == 'Team Rocket') {
                $team->setUsers($this->getUsersReferences('Rocket'));
            }
            if ($name == 'Team Nord') {
                $team->setUsers($this->getUsersReferences('Nord'));
            }
            if ($name == 'Team Süd') {
                $team->setUsers($this->getUsersReferences('Sued'));
            }

            $manager->persist($team);
            $manager->flush();

            $this->setReference('team-'.($key + 1), $team);
        }
    }

    /**
     * @param $team
     *
     * @return array
     */
    private function getUsersReferences($team)
    {
        $users = [];
        $users[] = $this->getReference('user-8');

        if ($team == 'Rocket') {
//            $userIds = [1, 2, 3];
//            foreach ($userIds as $userId) {
//                if (in_array($userId, $userIds)) {
//                    break;
//                }
//                $users[] = $this->getReference('user-'.$userId);
//            }
            $users[] = $this->getReference('user-1');
            $users[] = $this->getReference('user-2');
            $users[] = $this->getReference('user-3');
        }
        if ($team == 'Nord') {
            $users[] = $this->getReference('user-3');
            $users[] = $this->getReference('user-4');
            $users[] = $this->getReference('user-5');
        }
        if ($team == 'Sued') {
            $users[] = $this->getReference('user-6');
            $users[] = $this->getReference('user-7');
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
