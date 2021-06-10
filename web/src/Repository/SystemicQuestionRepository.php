<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\SystemicQuestion;

interface SystemicQuestionRepository
{
    public function save(SystemicQuestion $systemicQuestion): void;

    public function getRandomForClient(Client $client): SystemicQuestion;
}
