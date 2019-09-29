<?php
declare(strict_types=1);

namespace AppBundle\Query;

use AppBundle\Entity\Team;
use AppBundle\Entity\Teams;
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
