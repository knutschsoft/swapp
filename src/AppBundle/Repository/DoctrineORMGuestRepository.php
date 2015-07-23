<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Guest;
use Doctrine\ORM\EntityRepository;

class DoctrineORMGuestRepository extends EntityRepository implements GuestRepository
{
    public function findTrue()
    {
        return true;
    }

    public function save(Guest $guest)
    {
        $this->_em->persist($guest);
        $this->_em->flush();
    }
}
