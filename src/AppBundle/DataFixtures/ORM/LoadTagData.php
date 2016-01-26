<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Tag;
use AppBundle\Entity\Walk;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTagData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_TAGS = 4;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $names = [
            'Schule',
            'Gewalt (körperlich)',
            'Gewalt (verbal)',
            'Drogen',
            'Partnerschaft',
            'jobcenter',
            'Ausbildung',
            'Sexualität',
            'Politik',
            'Wohnungslosigkeit',
            'Schwangerschaft',
            'Polizei',
            'Sozialstunden',
            'Gemeinwesen',
            'Grundversorgung',
        ];

        $colors = ['Rot', 'Grün', 'Blau', 'Gelb'];

        foreach ($names as $key => $name) {
            $tag = new Tag();
            $tag->setName($name);
            $tag->setDescription('...');
            $tag->setWalks($this->getWalksReferences());
            $tag->setColor($colors[rand(0, count($colors) - 1)]);

            $manager->persist($tag);
            $manager->flush();

            $this->setReference('tag-' . ($key + 1), $tag);
        }
    }

    /**
     * @return Walk[]
     */
    private function getWalksReferences()
    {
        $walks = [];
        $walkIds = [];
        for ($i = 0; $i < LoadWalkData::NUM_WALKS; $i++) {
            $walkId = rand(1, LoadWalkData::NUM_WALKS);
            if (in_array($walkId, $walkIds)) {

                break;
            }
            $walkIds[] = $walkId;

            $walks[] = $this->getReference('walk-' . $walkId);
        }

        return $walks;
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return 4; // load after LoadWalkData
    }
}
