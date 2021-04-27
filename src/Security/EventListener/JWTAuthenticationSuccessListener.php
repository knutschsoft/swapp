<?php
declare(strict_types=1);

namespace App\Security\EventListener;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class JWTAuthenticationSuccessListener
{
    private UserRepository $userRepository;
    private IriConverterInterface $iriConverter;

    public function __construct(
        UserRepository $userRepository,
        IriConverterInterface $iriConverter
    ) {
        $this->userRepository = $userRepository;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
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
        $data['@id'] = $this->iriConverter->getIriFromItem($user);

        $event->setData($data);
    }
}
