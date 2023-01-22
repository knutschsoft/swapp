<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class JWTAuthenticationSuccessListener
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly IriConverterInterface $iriConverter
    ) {
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $this->userRepository->find($user->getId());

        // we will populate the user later via vue cause we can not get the serialized user
        $data['@id'] = $this->iriConverter->getIriFromResource($user);

        $event->setData($data);
    }
}
