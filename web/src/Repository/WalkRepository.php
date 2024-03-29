<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Walk;
use Doctrine\ORM\AbstractQuery;

interface WalkRepository
{
    /**
     * @param string $order
     * @param string $sort
     *
     * @return Walk[]
     */
    public function findAllOrderBy(string $order, string $sort): array;

    /**
     * @param User $user
     *
     * @return Walk[]
     */
    public function findAllUnfinishedByUser(User $user): array;

    public function getFindAllQuery(): AbstractQuery;

    /** @param int|string $id */
    public function findOneById(int|string $id): ?Walk;

    public function save(Walk $walk): void;

    /**
     * @param Client     $client
     * @param ?\DateTime $startTimeFrom
     * @param ?\DateTime $startTimeTo
     *
     * @return Walk[]
     */
    public function findForExport(Client $client, ?\DateTime $startTimeFrom, ?\DateTime $startTimeTo): array;

    public function remove(Walk $walk): void;
}
