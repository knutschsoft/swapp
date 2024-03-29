<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserEnableRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UserEnableRequestHandler
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(UserEnableRequest $request): User
    {
        $user = $request->user;
        $user->enable();
        $this->userRepository->save($request->user);

        return $request->user;
    }
}
