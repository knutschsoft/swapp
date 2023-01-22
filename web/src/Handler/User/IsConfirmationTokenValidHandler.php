<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\IsConfirmationTokenValidRequest;
use App\Entity\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class IsConfirmationTokenValidHandler
{
    public function __invoke(IsConfirmationTokenValidRequest $request): User
    {
        return $request->user;
    }
}
