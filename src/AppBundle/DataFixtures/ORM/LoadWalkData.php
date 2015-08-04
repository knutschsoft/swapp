<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SystemicQuestion;
use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWalkData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_WALKS = 10;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $rating = 0;
        $names = [
            'Köni runter',
            'Hechtviertel',
            'Lutherkirche',
        ];
        $systemicAnswer = 'I\'m feeling fine!';
        $walkReflection = 'This walk was a good one!';

        for ($i = 1; $i <= self::NUM_WALKS; $i++) {
            $rating++;

            $walk = new Walk();
            $walk->setName($names[rand(0, count($names) - 1)]);
            $walk->setRating(rand(0, 100));
            $walk->setStartTime(new \DateTime('-' . rand(50, 100) . 'minutes'));
            $walk->setEndTime(new \DateTime('-' . rand(0, 50) . 'minutes'));
            $walk->setSystemicAnswer($systemicAnswer . ' ' . $rating);
            $walk->setSystemicQuestion($this->getSystemicQuestionReference());
            $walk->setWalkReflection($walkReflection . ' ' . $rating);
            $walk->setWalkTeamMembers($this->getUsersReferences());
            $walk->setIsInternal(false);
            $walk->setHolidays("summer");
            $walk->setWeather("summer");

            $manager->persist($walk);
            $manager->flush();

            $this->addReference('walk-' . $i, $walk);
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
        return 3; // load after LoadSystemicQuestionData
    }
}
