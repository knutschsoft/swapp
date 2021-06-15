<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\UserEmailConfirmRequest;
use App\Entity\User;
use App\Notifier\RequestPasswordResetNotification;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final class UserEmailConfirmHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;
    private NotifierInterface $notifier;

    public function __construct(
        UserRepository $userRepository,
        NotifierInterface $notifier
    ) {
        $this->userRepository = $userRepository;
        $this->notifier = $notifier;
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
