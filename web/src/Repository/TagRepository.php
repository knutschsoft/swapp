<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Tag;

interface TagRepository
{
    public function save(Tag $tag): void;

    /** @return Tag[] */
    public function getTags(): array;

    public function updateTag(Tag $tag): void;

    public function findOneByColorAndNameAndClient(string $color, string $name, Client $client): Tag;
}
