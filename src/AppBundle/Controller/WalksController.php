<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Walk;
use AppBundle\Repository\WalkRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class WalksController
{
    private $templateEngine;
    private $walkRepository;

    /**
     * @param EngineInterface $templateEngine
     * @param WalkRepository  $walkRepository
     */
    public function __construct(EngineInterface $templateEngine, WalkRepository $walkRepository)
    {
        $this->templateEngine = $templateEngine;
        $this->walkRepository = $walkRepository;
    }

    public function homeScreenAction()
    {
        $walks = $this->walkRepository->findAll();

        $parameters = [
            'walks' => $walks,
        ];

        return $this->templateEngine->renderResponse(':Walks:walksHomeScreen.html.twig', $parameters);
    }

    public function showAction(Walk $walk)
    {
        $parameters = [
            'walk' => $walk,
        ];

        return $this->templateEngine->renderResponse(':Walks:show.html.twig', $parameters);
    }
}
