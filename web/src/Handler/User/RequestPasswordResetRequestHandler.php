<?php
declare(strict_types=1);

namespace App\Handler\User;

use App\Dto\User\RequestPasswordResetRequest;
use App\Notifier\RequestPasswordResetNotification;
use App\Repository\Exception\NotFoundException;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[AsMessageHandler]
final readonly class RequestPasswordResetRequestHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private NotifierInterface $notifier
    ) {
    }

    public function __invoke(RequestPasswordResetRequest $request): void
    {
        $email = $request->username;
        $honeypotEmail = $request->email;

        try {
            $userRequestedFor = $this->userRepository->findOneByEmailOrUsername($email);
        } catch (NotFoundException) {
            throw new \RuntimeException('This should never happen.');
        }
        if (!$userRequestedFor->isEnabled()) {
            throw new \RuntimeException('This should never happen.');
        }
        if (!$honeypotEmail) {
            $userRequestedFor->requestPassword();
            $this->userRepository->save($userRequestedFor);
            $notification = (new RequestPasswordResetNotification(
                $userRequestedFor->getConfirmationToken(),
                $userRequestedFor->getId(),
                $userRequestedFor->getUsername()
            ));
            $this->notifier->send($notification, new Recipient($userRequestedFor->getEmail()));
        }
    }
}
