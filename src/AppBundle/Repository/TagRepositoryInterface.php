<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Tag;

interface TagRepositoryInterface
{
    /**
     * @param Tag $tag
     */
    public function save(Tag $tag): void;

    /**
     * @return Tag[]
     */
    public function getTags(): array;

    /**
     * @param Tag $tag
     */
    public function updateTag(Tag $tag): void;

    /**
     * @return Tag[]
     */
    public function findAll(): array;
}
