<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\Walk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineORMWalkRepository extends ServiceEntityRepository implements WalkRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Walk::class);
    }

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
     * @param int|string $id
     *
     * @return Walk|null
     */
    public function findOneById($id): ?Walk
    {
        $walk = parent::findOneBy(['id' => $id]);
        \assert($walk instanceof Walk || null === $walk);

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
