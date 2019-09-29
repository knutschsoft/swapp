<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Guest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineORMGuestRepository extends ServiceEntityRepository implements GuestRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Guest::class);
    }

    public function save(Guest $guest): void
    {
        $this->_em->persist($guest);
        $this->_em->flush();
    }
}
