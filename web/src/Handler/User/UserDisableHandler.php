<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserDisableRequest;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UserDisableHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserDisableRequest $request): User
    {
        $user = $request->user;
        $user->disable();
        $this->userRepository->save($request->user);

        return $request->user;
    }
}
