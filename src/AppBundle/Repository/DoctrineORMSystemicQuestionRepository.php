<?php
namespace AppBundle\Repository;

use AppBundle\Entity\SystemicQuestion;
use Doctrine\ORM\EntityRepository;

class DoctrineORMSystemicQuestionRepository extends EntityRepository implements SystemicQuestionRepository
{
    public function findTrue()
    {
        return true;
    }

    public function save(SystemicQuestion $systemicQuestion)
    {
        $this->_em->persist($systemicQuestion);
        $this->_em->flush();
    }

    public function getRandom()
    {
        // TODO: Implement getRandom() method.
    }
}
