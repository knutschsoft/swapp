<?php
declare(strict_types=1);

namespace App\Notifier;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class UserCreatedNotification extends Notification implements EmailNotificationInterface
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;

        parent::__construct();
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = (new TemplatedEmail())
            ->to($recipient->getEmail())
            ->subject('Swapp | Account erstellt')
            ->htmlTemplate('emails/user_created.html.twig')
            ->context(
                [
                    'user' => $this->user,
                ]
            );

        return new EmailMessage($email);
    }
}
