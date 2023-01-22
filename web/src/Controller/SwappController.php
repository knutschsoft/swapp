<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SwappController extends AbstractController
{
    #[Route(path: '/{vueRouting}', name: 'swapp_home', requirements: ['vueRouting' => '^(?!pdf|api|js-logger|form|walkexport|_(profiler|wdt)).*'])]
    #[Route(path: '/passwort-aendern/{userId}/{confirmationToken}', name: 'user_password_reset')]
    #[Route(path: '/email-bestaetigen/{userId}/{confirmationToken}', name: 'user_email_confirm')]
    #[Route(path: '/', name: 'fallback')]
    public function __invoke(): Response
    {
        return $this->render(
            'swapp/swapp.html.twig',
            [

            ]
        );
    }
}
