<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\SystemicQuestion;

interface SystemicQuestionRepository
{
    public function save(SystemicQuestion $systemicQuestion): void;

    public function getRandom(): SystemicQuestion;
}
