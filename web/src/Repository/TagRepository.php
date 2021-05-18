<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;

interface TagRepository
{
    public function save(Tag $tag): void;

    /** @return Tag[] */
    public function getTags(): array;

    public function updateTag(Tag $tag): void;

    public function findOneByColor(string $color): Tag;

    public function findOneByName(string $name): tag;
}
