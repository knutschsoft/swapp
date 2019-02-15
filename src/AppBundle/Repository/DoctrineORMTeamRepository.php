<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Team;
use Doctrine\ORM\EntityRepository;

class DoctrineORMTeamRepository extends EntityRepository implements TeamRepositoryInterface
{
    /**
     * Finds all teams
     *
     * @return Team[]
     */
    public function findAll()
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

    /**
     * Persists the team to the database.
     *
     * @param Team $team
     */
    public function save(Team $team)
    {
        $this->_em->persist($team);
        $this->_em->flush();
    }
}
