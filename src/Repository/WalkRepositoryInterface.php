<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\Walk;
use Doctrine\ORM\AbstractQuery;

interface WalkRepositoryInterface
{
    /**
     * @return Walk[]
     */
    public function findAll(): array;

    /**
     * @return Walk[]
     */
    public function findAllOrderBy($order, $sort): array;

    /**
     * @param User $user
     *
     * @return Walk[]
     */
    public function findAllUnfinishedByUser(User $user): array;

    public function getFindAllQuery(): AbstractQuery;

    public function save(Walk $walk): void;

    public function update(Walk $walk): void;
}
