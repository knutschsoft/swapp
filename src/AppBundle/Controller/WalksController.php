<?php
namespace AppBundle\Controller;

use AppBundle\Repository\WalkRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class WalksController
{
    private $templateEngine;
    private $walkRepository;

    /**
     * @param EngineInterface $templateEngine
     * @param WalkRepository $walkRepository
     */
    public function __construct(EngineInterface $templateEngine, WalkRepository $walkRepository)
    {
        $this->templateEngine = $templateEngine;
        $this->walkRepository = $walkRepository;
    }

    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':Walks:walksHomeScreen.html.twig');
    }
}
