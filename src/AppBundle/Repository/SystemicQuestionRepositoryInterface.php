<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\SystemicQuestion;

interface SystemicQuestionRepositoryInterface
{
    /**
     * @param SystemicQuestion $systemicQuestion
     */
    public function save(SystemicQuestion $systemicQuestion): void;

    /**
     * @return SystemicQuestion
     */
    public function getRandom(): SystemicQuestion;
}
