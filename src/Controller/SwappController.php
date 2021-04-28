<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SwappController extends AbstractController
{
    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!pdf|api|js-logger|form|walkexport|_(profiler|wdt)).*"}, name="swapp_home")
     * @Route("/passwort-aendern/{userId}/{confirmationToken}", name="user_password_reset")
     * @Route("/", name="fallback")
     */
    public function __invoke(): Response
    {
        return $this->render(
            'swapp/swapp.html.twig',
            [
            ]
        );
    }
}
