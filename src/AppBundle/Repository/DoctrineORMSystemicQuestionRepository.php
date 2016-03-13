<?php
namespace AppBundle\Repository;

use AppBundle\Entity\SystemicQuestion;
use Doctrine\ORM\EntityRepository;

class DoctrineORMSystemicQuestionRepository extends EntityRepository implements SystemicQuestionRepositoryInterface
{
    /**
     * @param SystemicQuestion $systemicQuestion
     */
    public function save(SystemicQuestion $systemicQuestion)
    {
        $this->_em->persist($systemicQuestion);
        $this->_em->flush();
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return SystemicQuestion
     */
    public function getRandom()
    {
        $count = $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->getQuery()
            ->getSingleScalarResult();

        return $this->createQueryBuilder('u')
            ->setFirstResult(rand(0, $count - 1))
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}
