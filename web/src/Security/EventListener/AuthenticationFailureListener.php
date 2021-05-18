<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use App\Security\Exception\AccountDisabledException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Psr\Log\LoggerInterface;
use Webmozart\Assert\Assert;

class AuthenticationFailureListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $data = [
            'status' => '401 Unauthorized',
            'message' => 'Bad credentials, please verify that your username/password are correctly set.',
        ];

        $exception = $event->getException()->getPrevious() ?? $event->getException();

        if ($exception instanceof AccountDisabledException) {
            $data['message'] = 'Account disabled, please contact your admin for further details.';
        }

        $this->logger->error($exception->getMessage(), ['authentication.failure']);
        $this->logger->error($exception->getTraceAsString(), ['authentication.failure']);

        $jsonEncode = \json_encode($data);
        Assert::string($jsonEncode);
        $response = new JWTAuthenticationFailureResponse($jsonEncode);

        $event->setResponse($response);
    }
}
