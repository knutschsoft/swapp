<?php
namespace AppBundle\Repository;

use AppBundle\Entity\SystemicQuestion;

interface SystemicQuestionRepository
{
    public function findTrue();

    public function save(SystemicQuestion $systemicQuestion);

    public function getRandom();
}
