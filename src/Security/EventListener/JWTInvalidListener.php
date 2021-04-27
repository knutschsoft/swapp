<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

class JWTInvalidListener
{
    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $response = new JWTAuthenticationFailureResponse('Your token is invalid, please login again to get a new one', 403);

        $event->setResponse($response);
    }
}
