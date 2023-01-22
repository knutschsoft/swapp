<?php
declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class GetTokenController extends AbstractController
{
    #[Route("/api/users/getToken", methods: ['POST'])]
    public function getTokenAction(): Response
    {
        // The security layer will intercept this request
        return new Response('oha', 401);
    }
}
