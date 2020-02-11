<?php
declare(strict_types=1);

namespace App\Query;

use App\Entity\SystemicQuestion;
use App\Entity\SystemicQuestions;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineFindAllSystemicQuestions implements FindAllSystemicQuestions
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(): SystemicQuestions
    {
        return new SystemicQuestions(...$this->entityManager->getRepository(SystemicQuestion::class)->findAll());
    }
}
