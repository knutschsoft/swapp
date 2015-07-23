<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Guest;

interface GuestRepository
{
    public function findTrue();

    public function save(Guest $guest);
}
