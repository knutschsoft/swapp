<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SystemicQuestion;
use AppBundle\Entity\Walk;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSystemicQuestionData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_SYSTEMIC_QUESTION = 10;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $questions = [
            'Wie heißt du mit Vornamen?',
            'Wie groß ist Rudolf?',
            'Wie lautet das Codewort?',
            'Wann ist der Tag der deutschen Einheit?',
            'Wann war die französische Revolution?',
            'Ist Pluto ein Planet?',
            'Wie heiß ist die Sonne?',
            'Was ist die tiefste Temperatur?',
            'Wann ist das nächste Millenium?',
            'In welchem Jahr wurde der Nikolaus geboren?',
        ];

        foreach ($questions as $key => $questionString) {
            $question = new SystemicQuestion();
            $question->setQuestion($questionString);

            $manager->persist($question);
            $manager->flush();

            $this->addReference('systemicQuestion-' . ($key + 1), $question);
        }
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 1; // load before LoadWalkData
    }
}
