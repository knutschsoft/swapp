<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SystemicQuestion;

interface SystemicQuestionRepository
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
