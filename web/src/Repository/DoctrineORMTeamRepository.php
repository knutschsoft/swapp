<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Team>
 */
class DoctrineORMTeamRepository extends ServiceEntityRepository implements TeamRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @param int|string $id
     *
     * @return Team|null
     */
    public function findOneById($id): ?Team
    {
        return parent::findOneBy(['id' => $id]);
    }

    public function save(Team $team): void
    {
        $this->_em->persist($team);
        $this->_em->flush();
    }
}
