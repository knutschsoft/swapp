<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Walk;
use App\Repository\SystemicQuestionRepository;
use App\Repository\UserRepository;
use App\Repository\WalkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

final class WalksUnfishedController extends AbstractController
{
    private WalkRepository $walkRepository;
    private Security $security;
    private SystemicQuestionRepository $systemicQuestionRepository;
    private UserRepository $userRepository;

    public function __construct(
        WalkRepository $walkRepository,
        UserRepository $userRepository,
        SystemicQuestionRepository $systemicQuestionRepository,
        Security $security
    ) {
        $this->walkRepository = $walkRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
    }

    /**
     * @return Walk[]
     */
    public function __invoke(TokenStorageInterface $tokenStorage)
    {
        $user = $this->getUser();
        \assert($user instanceof UserInterface);
        \assert($user instanceof User);

        return $this->walkRepository->findAllUnfinishedByUser($user);
    }
}
