<?php
declare(strict_types=1);

namespace App\Query;

use App\Entity\Team;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineFindAllTeams implements FindAllTeams
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(): Teams
    {
        return new Teams(...$this->entityManager->getRepository(Team::class)->findAll());
    }
}
