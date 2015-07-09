<?php
namespace AppBundle\Controller;

use AppBundle\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class TagController
{
    private $templateEngine;
    private $tagRepository;

    /**
     * @param EngineInterface $templateEngine
     * @param TagRepository $tagRepository
     */
    public function __construct(EngineInterface $templateEngine, TagRepository $tagRepository)
    {
        $this->templateEngine = $templateEngine;
        $this->tagRepository = $tagRepository;
    }

    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':Walks:homeScreen.html.twig');
    }
}
