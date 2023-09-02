<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\ChangePasswordRequest;
use App\Entity\User;
use App\Notifier\ChangePasswordNotification;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
final readonly class ChangePasswordRequestHandler
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository $userRepository,
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(ChangePasswordRequest $request): User
    {
        $request->user->changePassword($request->password, $this->userPasswordHasher);
        $this->userRepository->save($request->user);
        $this->notifier->send(
            new ChangePasswordNotification($request->user->getUsername()),
            new Recipient($request->user->getEmail())
        );

        return $request->user;
    }
}
