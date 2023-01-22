<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;

class JWTAuthenticatedListener
{
    public function onJWTAuthenticated(JWTAuthenticatedEvent $event): void
    {
        // $token = $event->getToken();
        // $payload = $event->getPayload();
        // $token->setAttribute('uuid', $payload['uuid']);
        // $token->setAttribute('id', $payload['id']);
    }
}
