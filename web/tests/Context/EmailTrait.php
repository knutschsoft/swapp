<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Mailer\DataCollector\MessageDataCollector;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Event\MessageEvents;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

trait EmailTrait
{
    public function getMessageMailerEventsFromProfile(): MessageEvents
    {
        $restContext = $this->restContext;
        $token = $restContext->getMink()->getSession()->getResponseHeader('X-Debug-Token');

        /** @var ContainerInterface $serviceContainer */
        $serviceContainer = $this->kernel->getContainer()->get('test.service_container');
        $profiler = $serviceContainer->get(Profiler::class);

        $profile = $profiler->loadProfile($token);

        /** @var MessageDataCollector $mailCollector */
        $mailCollector = $profile->getCollector('mailer');

        return $mailCollector->getEvents();
    }

    /**
     * @param string|null $transport
     *
     * @return MessageEvents[]
     */
    public function getMailerEvents(?string $transport = null): array
    {
        return $this->getMessageMailerEventsFromProfile()->getEvents($transport);
    }

    public function getMailerEvent(int $index = 0, ?string $transport = null): ?MessageEvent
    {
        return $this->getMailerEvents($transport)[$index] ?? null;
    }

    public function getMailerMessages(?string $transport = null): array
    {
        return $this->getMessageMailerEventsFromProfile()->getMessages($transport);
    }

    public function getMailerMessage(int $index = 0, ?string $transport = null): ?Email
    {
        return $this->getMailerMessages($transport)[$index] ?? null;
    }

    public function getMailerMessagesForAddress(string $address, ?string $transport = null): array
    {
        $messages = $this->getMailerMessages($transport);

        return \array_filter(
            $messages,
            static function (Email $message) use ($address) {
                $addresses = \array_merge($message->getTo(), $message->getCc());
                $addressStrings = \array_map(
                    static function (Address $address) {
                        return $address->getAddress();
                    },
                    $addresses
                );

                return \in_array($address, $addressStrings, true);
            }
        );
    }

    private function countEmails(MessageEvents $events): int
    {
        $count = 0;
        foreach ($events->getEvents() as $event) {
            if (!$event->isQueued()) {
                ++$count;
            }
        }

        return $count;
    }
}
