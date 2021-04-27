<?php
declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LogoutController extends AbstractController
{
    /**
     * @throws \RuntimeException
     *
     * Rest\Route("/api/security/logout", name="logout")
     * Rest\Route("/api/security/login_check", name="login_check")
     */
    public function __invoke(): void
    {
        throw new \RuntimeException('This should not be reached!');
    }
}
