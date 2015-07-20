<?php
namespace AppBundle\Controller;

use AppBundle\Repository\WayPointRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class WayPointController
{
    private $templateEngine;
    private $wayPointRepository;

    /**
     * @param EngineInterface $templateEngine
     * @param WayPointRepository $wayPointRepository
     */
    public function __construct(EngineInterface $templateEngine, WayPointRepository $wayPointRepository)
    {
        $this->templateEngine = $templateEngine;
        $this->wayPointRepository = $wayPointRepository;
    }

    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':Walks:walksHomeScreen.html.twig');
    }
}
