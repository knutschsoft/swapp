<?php
declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class LocationName
{
    #[Groups(['locationName:read'])]
    public string $name;
}
