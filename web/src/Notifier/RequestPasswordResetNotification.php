<?php
declare(strict_types=1);

namespace App\Notifier;

use App\Value\ConfirmationToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class RequestPasswordResetNotification extends Notification implements EmailNotificationInterface
{
    private ConfirmationToken $confirmationToken;
    private string $username;
    private int $userId;

    public function __construct(
        ConfirmationToken $confirmationToken,
        int $userId,
        string $username
    ) {
        $this->confirmationToken = $confirmationToken;
        $this->username = $username;
        $this->userId = $userId;

        parent::__construct();
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = (new TemplatedEmail())
            ->to($recipient->getEmail())
            ->subject('Swapp | Passwort ändern')
            ->htmlTemplate('emails/request_password_reset.html.twig')
            ->context(
                [
                    'confirmationToken' => $this->confirmationToken,
                    'username' => $this->username,
                    'userId' => $this->userId,
                ]
            );

        return new EmailMessage($email);
    }
}
