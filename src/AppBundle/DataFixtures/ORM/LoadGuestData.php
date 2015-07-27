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
            'Dupreet',
            'Michelle',
            'Carolin',
            'Elke',
        ];

        foreach ($names as $key => $name) {
            $guest = new Guest();
            $guest->setName($name);
            $guest->setEmail($name . '@t-online.de');
            $guest->setWalk($this->getWalkReference());

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
        return 2; // load after LoadWalkData
    }
}
