<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserCreateRequest;
use App\Entity\User;
use App\Notifier\UserCreatedNotification;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final class UserCreateHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(UserCreateRequest $request): User
    {
        $user = User::fromUserCreateRequest($request, $this->userPasswordHasher);
        $this->userRepository->save($user);
        $this->notifier->send(
            new UserCreatedNotification($user),
            new Recipient($user->getEmail())
        );

        return $user;
    }
}
