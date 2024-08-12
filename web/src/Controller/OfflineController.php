<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfflineController extends AbstractController
{
    #[Route(path: '/offline', name: 'swapp_offline')]
    public function __invoke(): Response
    {
        return new Response('your are offline');
    }
}
