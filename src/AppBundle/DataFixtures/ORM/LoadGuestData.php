<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Guest;
use AppBundle\Entity\Walk;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadGuestData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $names = [
            'achim_gast',
            'drogenfahnder_gast',
        ];

        foreach ($names as $key => $name) {
            $guest = new Guest();
            $guest->setName($name);
            $guest->setEmail($name.'@bla.org');
//            $guest->setWalk($this->getWalkReference());

            $manager->persist($guest);
        }

        $manager->flush();
    }

    /**
     * @return Walk
     */
    private function getWalkReference()
    {
        return $this->getReference('walk-' . rand(1, LoadWalkData::NUM_WALKS));
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 4; // load after LoadWalkData
    }
}
