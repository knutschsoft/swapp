<?php
namespace AppBundle\Controller;

use QafooLabs\MVC\Flash;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class ControllerAsAServiceController
{
    private $templateEngine;

    /**
     * @param EngineInterface $templateEngine
     */
    public function __construct(EngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @return Response
     */
    public function exampleAction()
    {
        return $this->templateEngine->renderResponse(':DirectoryAsAService:example.html.twig');
    }

    /**
     * @param Flash     $flash
     * @param \stdClass $stdClass
     * @param \stdClass $anotherStdClass
     *
     * @return Response
     */
    public function paramConverterAction(Flash $flash, \stdClass $stdClass, \stdClass $anotherStdClass)
    {
        $viewParams = array(
            'stdClass' => $stdClass,
            'anotherStdClass' => $anotherStdClass,
            'flash' => $flash,
        );

        return $this->templateEngine->renderResponse(':DirectoryAsAService:example.html.twig', $viewParams);
    }
}
