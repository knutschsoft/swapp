<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class WalksController
{
    private $templateEngine;

    /**
     * @param EngineInterface $templateEngine
     */
    public function __construct(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':Walks:homeScreen.html.twig');
    }
}
