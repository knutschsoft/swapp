<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AgeRange;

interface AgeRangeRepositoryInterface
{
    /**
     * @param AgeRange $range
     */
    public function save(AgeRange $range);

    /**
     * @return AgeRange[]
     */
    public function findAll();

    /**
     * @param $id
     * @return AgeRange
     */
    public function getRange($id);
}
