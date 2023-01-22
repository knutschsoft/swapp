<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class LogoutController
{
    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function __invoke(): void
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
