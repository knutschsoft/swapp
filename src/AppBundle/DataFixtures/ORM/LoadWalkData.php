<?php
namespace AppBundle\DataFixtures\ORM;

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
        $systemicQuestion = 'How are you?';
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
            $walk->setSystemicQuestion($systemicQuestion . ' ' . $rating);
            $walk->setWalkReflection($walkReflection . ' ' . $rating);

            $manager->persist($walk);
            $manager->flush();

            $this->addReference('walk-' . $i, $walk);
        }
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 1; // load after LoadGuestData, LoadUserData and LoadTagData
    }
}
