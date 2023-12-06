<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserChangeRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UserChangeRequestHandler
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function __invoke(UserChangeRequest $request): User
    {
        $user = $request->user;
        $user->setUsername($request->username);
        $user->setEmail($request->email);
        $user->setRoles($request->roles);
        $user->updateClient($request->client);
        $this->userRepository->save($request->user);

        return $request->user;
    }
}
