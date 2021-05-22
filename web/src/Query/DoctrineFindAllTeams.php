<?php
declare(strict_types=1);

namespace App\Query;

use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineFindAllTeams implements FindAllTeams
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Team[]
     */
    public function __invoke(): array
    {
        return $this->entityManager->getRepository(Team::class)->findAll();
    }
}
