<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Tests\Session\Flash\FlashBagTest;

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

    public function exampleAction()
    {
        return $this->templateEngine->renderResponse(':DirectoryAsAService:example.html.twig');
    }

    public function paramConverterAction(FlashBag $flashBag, \stdClass $stdClass, \stdClass $anotherStdClass)
    {
        $viewParams = array(
            'stdClass' => $stdClass,
            'anotherStdClass' => $anotherStdClass,
            'flashBag' => $flashBag,
        );

        return $this->templateEngine->renderResponse(':DirectoryAsAService:example.html.twig', $viewParams);
    }
}
