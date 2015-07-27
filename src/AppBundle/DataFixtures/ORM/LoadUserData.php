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
    const NUM_USERS = 4;

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
        ];

        foreach ($names as $key => $name) {
            $user = new User();
            $user->setName($name);

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
            //$teams[] = $this->getReference('team-' . rand(1, 10));
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
            $walks[] = $this->getReference('walk-' . rand(1, LoadWalkData::NUM_WALKS));
        }

        return $walks;
    }


    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 2; // load after LoadWalkData
    }
}
