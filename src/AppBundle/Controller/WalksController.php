<?php
namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use AppBundle\Repository\WalkRepository;
use AppBundle\Repository\WayPointRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Routing\RouterInterface;

class WalksController
{
    private $templateEngine;
    private $walkRepository;
    private $router;
    private $formFactory;
    private $wayPointRepository;

    /**
     * @param EngineInterface      $templateEngine
     * @param FormFactoryInterface $formFactory
     * @param WalkRepository       $walkRepository
     * @param RouterInterface      $router
     * @param WayPointRepository   $wayPointRepository
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WalkRepository $walkRepository,
        RouterInterface $router,
        WayPointRepository $wayPointRepository
    ) {
        $this->formFactory = $formFactory;
        $this->templateEngine = $templateEngine;
        $this->walkRepository = $walkRepository;
        $this->router = $router;
        $this->wayPointRepository = $wayPointRepository;
    }

    public function homeScreenAction(User $user)
    {
        $walks = $this->walkRepository->findAll();
        $teams = $user->getTeams();
        $parameters = [
            'walks' => $walks,
            'teams' => $teams,
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

    public function createWalkFormAction(Walk $walk)
    {
        $form = $this->formFactory->create(
            'app_create_walk',
            $walk,
            array(
                'action' => $this->router->generate('walk_create'),
            )
        );

        return $this->templateEngine->renderResponse(
            ':Walks:walkForm.html.twig',
            array(
                'form' => $form->createView(),
                'wayPoints' => $this->wayPointRepository->findAllFor($walk->getId()),
                'systemicQuestion' => $walk->getSystemicQuestion(),
            )
        );
    }

    public function createWalkAction(Request $request, FlashBag $flashBag)
    {
        $form = $this->formFactory->create('app_create_walk', new Walk());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $walk = $form->getData();
            $walk->setIsInternal(0);
            $this->walkRepository->update($walk);
            $flashBag->add(
                'notice',
                'Runde wurde erfolgreich erstellt.'
            );

            $url = $this->router->generate('walk_home_screen');

            return new RedirectResponse($url);
        }

        return $this->templateEngine->renderResponse(
            ':Walks:walkForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
