<?php
declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => ['guestName:read']],
)]
class GuestName
{
    public function __construct(
        #[Groups(['guestName:read', 'walk:read'])]
        public string $name
    ) {
    }
}
