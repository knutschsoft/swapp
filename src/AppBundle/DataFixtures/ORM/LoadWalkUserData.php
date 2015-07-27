<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\WalkUser;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWalkUserData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_WALK_USER = 20;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $walkUserCombination = [];

        for ($i = 1; $i <= self::NUM_WALK_USER; $i++) {
            $userId = rand(1, LoadUserData::NUM_USERS);
            $walkId = rand(1, LoadWalkData::NUM_WALKS);

            while (isset($walkUserCombination[$walkId]) && in_array($userId, $walkUserCombination[$walkId])) {
                $userId = rand(1, LoadUserData::NUM_USERS);
                $walkId = rand(1, LoadWalkData::NUM_WALKS);
            }

            $walkUserCombination[$walkId][] = $userId;

            $walkUser = new WalkUser();
            $walkUser->setUserId($this->getReference('user-' . $userId)->getId());
            $walkUser->setWalkId($this->getReference('walk-' . $walkId)->getId());

            $manager->persist($walkUser);
            $manager->flush();
        }
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 3; // load after LoadWalkData and LoadTagData
    }
}
