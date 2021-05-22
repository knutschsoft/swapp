<?php
declare(strict_types=1);

namespace App\Query;

use App\Entity\SystemicQuestion;

interface FindAllSystemicQuestions
{
    /**
     * @return SystemicQuestion[]
     */
    public function __invoke(): array;
}
