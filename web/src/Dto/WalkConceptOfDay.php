<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class WalkConceptOfDay
{
    #[Groups(['walkConceptOfDay:read'])]
    public string $conceptOfDay;
}
