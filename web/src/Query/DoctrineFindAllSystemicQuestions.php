<?php
declare(strict_types=1);

namespace App\Query;

use App\Entity\SystemicQuestion;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineFindAllSystemicQuestions implements FindAllSystemicQuestions
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return SystemicQuestion[]
     */
    public function __invoke(): array
    {
        return $this->entityManager->getRepository(SystemicQuestion::class)->findAll();
    }
}
