<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SystemicQuestion;
use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use AppBundle\Entity\WayPoint;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWayPointData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_WAY_POINTS = 100;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $rating = 0;
        $names = [
            'Josephinenstraße 4',
            'Vorm Netto',
            'Schauburg',
            'Assieck',
            'Ackis Bar',
        ];
        $systemicAnswer = 'I\'m feeling fine!';
        $walkReflection = 'This walk was a good one!';

        for ($i = 1; $i <= self::NUM_WAY_POINTS; $i++) {
            $rating++;

            $wayPoint = new WayPoint();
            $wayPoint->setLocationName($names[rand(0, count($names) - 1)]);
            $wayPoint->setFemalesAdultCount(rand(0, 20));
            $wayPoint->setFemalesChildCount(rand(0, 20));
            $wayPoint->setFemalesKidCount(rand(0, 20));
            $wayPoint->setFemalesYouthCount(rand(0, 20));
            $wayPoint->setMalesAdultCount(rand(0, 20));
            $wayPoint->setMalesChildCount(rand(0, 20));
            $wayPoint->setMalesKidCount(rand(0, 20));
            $wayPoint->setMalesYouthCount(rand(0, 20));
            $wayPoint->setIsMeeting(rand(0, 1));
            $wayPoint->setNote('note Lorem ipsum tralalala');
            $wayPoint->setWalk($this->getReference('walk-' . rand(1, LoadWalkData::NUM_WALKS)));

            $manager->persist($wayPoint);
            $manager->flush();
        }
    }

    /**
     * @return SystemicQuestion
     */
    private function getSystemicQuestionReference()
    {

        return $this->getReference('systemicQuestion-' . rand(1, LoadSystemicQuestionData::NUM_SYSTEMIC_QUESTION));
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
        return 4; // load after LoadWalkData
    }
}
