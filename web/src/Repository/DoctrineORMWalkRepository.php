<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\Walk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Walk|null find($id, $lockMode = null, $lockVersion = null)
 * @method Walk|null findOneBy(array $criteria, array $orderBy = null)
 * @method Walk[]    findAll()
 * @method Walk[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Walk>
 */
class DoctrineORMWalkRepository extends ServiceEntityRepository implements WalkRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Walk::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllOrderBy(string $order, string $sort = 'asc'): array
    {
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select()
            ->orderBy($order, $sort);
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $user
     *
     * @return Walk[]
     */
    public function findAllUnfinishedByUser(User $user): array
    {
        $teamNames = [];
        foreach ($user->getTeams() as $team) {
            $teamNames[] = $team->getName();
        }
        $date = new \DateTime('-24 hour');
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select()
            ->where('walk.teamName IN (:teamNames)')
            ->setParameter('teamNames', $teamNames)
            ->andWhere('walk.systemicAnswer = :systemicAnswer')
            ->setParameter('systemicAnswer', '')
            ->andWhere('walk.startTime >= :date')
            ->setParameter('date', $date)
            ->orderBy('walk.startTime', 'DESC');
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function getFindAllQuery(): AbstractQuery
    {
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select();

        return $queryBuilder->getQuery();
    }

    /**
     * @inheritDoc
     */
    public function findForExport(?\DateTime $startTimeFrom, ?\DateTime $startTimeTo): array
    {
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select();

        if ($startTimeFrom) {
            $startTimeFrom->setTime(0, 0, 0);
            $queryBuilder
                ->where('walk.startTime > :startTimeFrom')
                ->setParameter('startTimeFrom', $startTimeFrom);
        }
        if ($startTimeTo) {
            $startTimeTo->setTime(23, 59, 59);
            $queryBuilder
                ->where('walk.startTimeTo < :')
                ->setParameter('startTimeTo', $startTimeTo);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int|string $id
     *
     * @return Walk|null
     */
    public function findOneById(int|string $id): ?Walk
    {
        return parent::findOneBy(['id' => $id]);
    }

    public function save(Walk $walk): void
    {
        $this->_em->persist($walk);
        $this->_em->flush();
    }

    public function update(Walk $walk): void
    {
        $this->_em->merge($walk);
        $this->_em->flush();
    }
}
