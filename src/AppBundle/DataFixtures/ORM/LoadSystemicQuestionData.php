<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SystemicQuestion;
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
            'Was war heute der größte Erfolg bei dem Angebot?',
            'Was würden Deine Kolleg_innen zum heutigen Angebot sagen?',
            'Worin unterscheidet sich das Angebot heute von einem vorhergehenden?',
            'Wie würdest Du das Angebot auf einer Skala von 1-10 einordnen? Warum?',
            'Was müsste geschehen das es heute kein schönes Angebot war?',
            'Was denkt ihr, würden die Adressat_innen zum heutigen Angebot sagen?',
            'Was wäre heute geschehen wenn auf einmal alles zu 100% perfekt gelaufen ist?',
            'Wie müsstet ihr eure Zeit verbringen damit euer Angebot am wirkungsvollsten ist?',
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
