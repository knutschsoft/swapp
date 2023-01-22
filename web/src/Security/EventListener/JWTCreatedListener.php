<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request) {
            $payload = $event->getData();
            $payload['ip'] = $request->getClientIp();
            $event->setData($payload);
        }
    }
}
