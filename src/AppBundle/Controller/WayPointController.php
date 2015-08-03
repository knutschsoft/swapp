<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\Walk;
use AppBundle\Entity\WayPoint;
use AppBundle\Repository\SystemicQuestionRepository;
use AppBundle\Repository\WalkRepository;
use AppBundle\Repository\WayPointRepository;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Routing\RouterInterface;

class WayPointController
{
    private $templateEngine;
    private $wayPointRepository;
    private $router;
    private $formFactory;
    private $userManager;

    /**
     * @param EngineInterface            $templateEngine
     * @param FormFactoryInterface       $formFactory
     * @param WayPointRepository         $wayPointRepository
     * @param RouterInterface            $router
     * @param WalkRepository             $walkRepository
     * @param SystemicQuestionRepository $systemicQuestionRepository
     * @param UserManagerInterface       $userManager
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WayPointRepository $wayPointRepository,
        RouterInterface $router,
        WalkRepository $walkRepository,
        SystemicQuestionRepository $systemicQuestionRepository,
        UserManagerInterface $userManager
    ) {
        $this->templateEngine = $templateEngine;
        $this->wayPointRepository = $wayPointRepository;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->walkRepository = $walkRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
        $this->userManager = $userManager;
    }

    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':WayPoint:wayPointForm.html.twig');
    }

    public function createWayPointFormAction(Team $team)
    {
        $walk = new Walk();

        $walk->setName("placeholder_name");
        $walk->setIsInternal(true);
        $walk->setStartTime(new \DateTime());
        $walk->setEndTime(new \DateTime());
        $walk->setRating(1);
        $walk->setSystemicAnswer("placeholder_answer");
        $walk->setSystemicQuestion($this->systemicQuestionRepository->getRandom());
        $walk->setWalkReflection("placeholder_reflection");

        $walkId = $this->walkRepository->save($walk);

        foreach ($team->getUsers() as $user) {
            $user->setWalks([$walk]);
            $this->userManager->updateUser($user, true);
        }
        $wayPoint = new WayPoint();
        $form = $this->formFactory->create(
            'app_create_way_point',
            $wayPoint,
            array(
                'action' => $this->router->generate('way_point_create', array('team' => $team->getId())),
            )
        );

        return $this->templateEngine->renderResponse(
            ':WayPoint:wayPointForm.html.twig',
            array(
                'form' => $form->createView(),
                'wayPoints' => $this->wayPointRepository->findAllFor($team->getId()),
                'walkId' => $walkId,
            )
        );
    }

    public function createWayPointAction(Request $request, FlashBag $flashBag, Team $team)
    {
        $form = $this->formFactory->create('app_create_way_point', new WayPoint());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $wayPoint = $form->getData();
            $this->wayPointRepository->save($wayPoint);

            if ($form->get('createWayPoint')->isClicked()) {
                // probably redirect to the add page again
                $flashBag->add(
                    'notice',
                    'Wegpunkt wurde erfolgreich erstellt.'
                );

                $url = $this->router->generate('way_point_create_form', array('team' => $team->getId()));

                return new RedirectResponse($url);
            }
            if ($form->get('createWalk')->isClicked()) {
                // probably redirect to the add page again
                $flashBag->add(
                    'notice',
                    'Wegpunkt wurde erfolgreich erstellt.'
                );

                $url = $this->router->generate('walk_create_form');

                return new RedirectResponse($url);
            }
        }

        return $this->templateEngine->renderResponse(
            ':WayPoint:wayPointForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
