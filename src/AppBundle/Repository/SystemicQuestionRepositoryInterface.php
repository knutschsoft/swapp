<?php
namespace AppBundle\Repository;

use AppBundle\Entity\SystemicQuestion;

interface SystemicQuestionRepositoryInterface
{
    /**
     * @param SystemicQuestion $systemicQuestion
     */
    public function save(SystemicQuestion $systemicQuestion);

    /**
     * @return SystemicQuestion
     */
    public function getRandom();
}
