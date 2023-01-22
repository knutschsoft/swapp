<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserEmailConfirmRequest;
use App\Entity\User;
use App\Notifier\RequestPasswordResetNotification;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsMessageHandler]
final class UserEmailConfirmHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly NotifierInterface $notifier
    ) {
    }

    public function __invoke(UserEmailConfirmRequest $request): User
    {
        $request->user->enable();
        $request->user->requestPassword();
        $this->userRepository->save($request->user);

        $notification = (new RequestPasswordResetNotification(
            $request->user->getConfirmationToken(),
            $request->user->getId(),
            $request->user->getUsername()
        ));
        $this->notifier->send($notification, new Recipient($request->user->getEmail()));

        return $request->user;
    }
}
