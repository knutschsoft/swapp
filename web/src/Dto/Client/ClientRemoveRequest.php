<?php
declare(strict_types=1);

namespace App\Dto\Client;

use App\Entity\Client;
use App\Validator\Constraints as AppAssert;

final class ClientRemoveRequest
{
    #[AppAssert\ClientRequirements]
    #[AppAssert\ClientNotOfCurrentUser]
    public Client $client;
}
