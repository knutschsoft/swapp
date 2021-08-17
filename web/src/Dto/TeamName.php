<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class TeamName
{
    #[Groups(['teamName:read'])]
    public string $name;
}
