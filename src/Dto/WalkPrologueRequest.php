<?php
declare(strict_types=1);

namespace App\Dto;

use App\Entity\Team;
use Symfony\Component\Validator\Constraints as Assert;

final class WalkPrologueRequest
{
    /**
     * @var Team
     *
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\Type(type="App\Entity\Team")
     */
    public Team $team;
}
