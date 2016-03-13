<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Tag;

interface TagRepositoryInterface
{
    /**
     * @param Tag $tag
     */
    public function save(Tag $tag);

    /**
     * @return Tag[]
     */
    public function getTags();

    /**
     * @param Tag $tag
     */
    public function updateTag(Tag $tag);

    /**
     * @return Tag[]
     */
    public function findAll();
}
