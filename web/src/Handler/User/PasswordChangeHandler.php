<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\PasswordChangeRequest;
use App\Entity\User;
use App\Notifier\ChangePasswordNotification;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordChangeHandler implements MessageHandlerInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private UserRepository $userRepository;
    private NotifierInterface $notifier;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        NotifierInterface $notifier
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->userRepository = $userRepository;
        $this->notifier = $notifier;
    }

    public function __invoke(PasswordChangeRequest $request): User
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
