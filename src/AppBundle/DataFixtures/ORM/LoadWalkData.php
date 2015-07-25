<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Walk;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWalkData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $rating = 0;
        $name = 'Karl';
        $systemicQuestion = 'How are you?';
        $systemicAnswer = 'I\'m feeling fine!';
        $walkReflection = 'This walk was a good one!';

        for ($i = 0; $i < 100; $i++) {
            $rating++;

            $userAdmin = new Walk();
            $userAdmin->setName($name . ' ' . $rating);
            $userAdmin->setRating(rand(0, 100));
            $userAdmin->setStartTime(new \DateTime('-' . rand(50, 100) . 'minutes'));
            $userAdmin->setEndTime(new \DateTime('-' . rand(0, 50) . 'minutes'));
            $userAdmin->setSystemicAnswer($systemicAnswer . ' ' . $rating);
            $userAdmin->setSystemicQuestion($systemicQuestion . ' ' . $rating);
            $userAdmin->setWalkReflection($walkReflection . ' ' . $rating);

            $manager->persist($userAdmin);
        }

        $manager->flush();
    }
}
