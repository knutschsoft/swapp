<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserChangeRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UserChangeHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserChangeRequest $request): User
    {
        $user = $request->user;
        $user->setUsername($request->username);
        $user->setEmail($request->email);
        $user->setRoles($request->roles);
        $this->userRepository->save($request->user);

        return $request->user;
    }
}
