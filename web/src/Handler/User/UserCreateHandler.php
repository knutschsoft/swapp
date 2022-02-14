<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserCreateRequest;
use App\Entity\User;
use App\Notifier\UserCreatedNotification;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserCreateHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;
    private NotifierInterface $notifier;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        NotifierInterface $notifier
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->notifier = $notifier;
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
