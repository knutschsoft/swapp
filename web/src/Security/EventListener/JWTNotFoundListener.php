<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;

class JWTNotFoundListener
{
    public function onJWTNotFound(JWTNotFoundEvent $event): void
    {
        // $data = [
        //     'status'  => '403 Forbidden',
        //     'message' => 'Missing token',
        // ];
        //
        // $response = new Response($data, 403);
        //
        // $event->setResponse($response);
    }
}
