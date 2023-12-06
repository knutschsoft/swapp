<?php
declare(strict_types=1);

namespace App\Notifier;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class ChangePasswordNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(private readonly string $username)
    {
        parent::__construct();
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = (new TemplatedEmail())
            ->to($recipient->getEmail())
            ->subject('Swapp | Passwort geändert')
            ->htmlTemplate('emails/change_password.html.twig')
            ->context(
                [
                    'username' => $this->username,
                ]
            );

        return new EmailMessage($email);
    }
}
