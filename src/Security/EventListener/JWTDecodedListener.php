<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;

class JWTDecodedListener
{
    public function onJWTDecoded(JWTDecodedEvent $event): void
    {
        // $request = $this->requestStack->getCurrentRequest();
        //
        // $payload = $event->getPayload();
        //
        // if (!isset($payload['ip']) || $payload['ip'] !== $request->getClientIp()) {
        //     $event->markAsInvalid();
        // }
    }
}
