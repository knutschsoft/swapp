<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SystemicQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineORMSystemicQuestionRepository extends ServiceEntityRepository implements SystemicQuestionRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SystemicQuestion::class);
    }

    public function save(SystemicQuestion $systemicQuestion): void
    {
        $this->_em->persist($systemicQuestion);
        $this->_em->flush();
    }

    /**
     * @return SystemicQuestion
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getRandom(): SystemicQuestion
    {
        $count = $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->createQueryBuilder('u')
            ->setFirstResult(\rand(0, $count - 1))
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}
