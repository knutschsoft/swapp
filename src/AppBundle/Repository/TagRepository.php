<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Tag;

interface TagRepository
{
    public function findTrue();

    public function save(Tag $tag);

    public function getTags();
}
