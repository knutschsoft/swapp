<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineORMTeamRepository extends ServiceEntityRepository implements TeamRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /** @return Team[] */
    public function findAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('team')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param int|string $id
     *
     * @return Team|null
     */
    public function findOneById($id): ?Team
    {
        $team = parent::findOneBy(['id' => $id]);
        \assert($team instanceof Team || null === $team);

        return $team;
    }

    public function save(Team $team): void
    {
        $this->_em->persist($team);
        $this->_em->flush();
    }
}
