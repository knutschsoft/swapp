<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\WalkTag;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWalkTagData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NUM_WALK_TAGS = 20;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $walkTagCombination = [];
        for ($i = 1; $i <= self::NUM_WALK_TAGS; $i++) {
            $tagId = rand(1, LoadTagData::NUM_TAGS);
            $walkId = rand(1, LoadWalkData::NUM_WALKS);

            while (isset($walkTagCombination[$walkId]) && in_array($tagId, $walkTagCombination[$walkId])) {
                $tagId = rand(1, LoadTagData::NUM_TAGS);
                $walkId = rand(1, LoadWalkData::NUM_WALKS);
            }

            $walkTagCombination[$walkId][] = $tagId;

            $walkTag = new WalkTag();
            $walkTag->setTagId($this->getReference('tag-' . $tagId)->getId());
            $walkTag->setWalkId($this->getReference('walk-' . $walkId)->getId());

            $manager->persist($walkTag);
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
