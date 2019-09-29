<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

class LogoutController
{
    /**
     * @Route(name="app_logout", methods={"GET"}, path="/logout")
     *
     * @throws \Exception
     */
    public function __invoke(): void
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
