<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineORMTeamRepository extends ServiceEntityRepository implements TeamRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return Team[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('team')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function findOneById($id): ?Team
    {
        /** @var Team|null $team */
        $team = parent::findOneBy(['id' => $id]);

        return $team;
    }

    public function save(Team $team): void
    {
        $this->_em->persist($team);
        $this->_em->flush();
    }
}
