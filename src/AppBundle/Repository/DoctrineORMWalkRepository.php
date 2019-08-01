<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

class DoctrineORMWalkRepository extends EntityRepository implements WalkRepositoryInterface
{
    /**
     * @return Walk[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('walk')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @return Walk[]
     */
    public function findAllOrderBy($order, $sort = 'asc'): array
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
        $query = $queryBuilder->getQuery();

        return $query;
    }

    public function findOneById($id): ?Walk
    {
        /** @var Walk|null $walk */
        $walk = parent::findOneBy(['id' => $id]);

        return $walk;
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
