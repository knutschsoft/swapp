<?php
declare(strict_types=1);

namespace App\Query;

use App\Entity\SystemicQuestions;

interface FindAllSystemicQuestions
{
    public function __invoke(): SystemicQuestions;
}
