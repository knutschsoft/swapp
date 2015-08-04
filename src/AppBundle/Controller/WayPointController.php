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

    public function startWalkWithWayPointAction(Team $team)
    {
        $walk = new Walk();

        $walk->setName("placeholder_name");
        $walk->setIsInternal(true);
        $walk->setStartTime(new \DateTime());
        $walk->setEndTime(new \DateTime());
        $walk->setRating(1);
        $walk->setSystemicAnswer("placeholder_answer");
        $walk->setSystemicQuestion($this->systemicQuestionRepository->getRandom()->getQuestion());
        $walk->setWalkReflection("placeholder_reflection");
        $walk->setWeather("placeholder_weather");
        $walk->setHolidays("placeholder_holidays");

        $this->walkRepository->save($walk);

        foreach ($team->getUsers() as $user) {
            $user->setWalks([$walk]);
            $this->userManager->updateUser($user, true);
        }
        $wayPoint = new WayPoint();
        $form = $this->formFactory->create(
            'app_create_way_point',
            $wayPoint,
            array(
                'action' => $this->router->generate('way_point_create', array('walkId' => $walk->getId())),
            )
        );

        return $this->templateEngine->renderResponse(
            ':WayPoint:wayPointForm.html.twig',
            array(
                'form' => $form->createView(),
                'wayPoints' => $this->wayPointRepository->findAllFor($walk->getId()),
            )
        );
    }

    public function updateWalkWithWayPointAction(Walk $walk)
    {
        $wayPoint = new WayPoint();
        $form = $this->formFactory->create(
            'app_create_way_point',
            $wayPoint,
            array(
                'action' => $this->router->generate('way_point_create', array('walkId' => $walk->getId())),
            )
        );

        return $this->templateEngine->renderResponse(
            ':WayPoint:wayPointForm.html.twig',
            array(
                'form' => $form->createView(),
                'wayPoints' => $this->wayPointRepository->findAllFor($walk->getId()),
            )
        );
    }

    public function createWayPointAction(Request $request, FlashBag $flashBag, Walk $walk)
    {
        $form = $this->formFactory->create('app_create_way_point', new WayPoint());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $wayPoint = $form->getData();
            $wayPoint->setWalk($walk);
            $this->wayPointRepository->save($wayPoint);

            $flashBag->add(
                'notice',
                'Wegpunkt wurde erfolgreich erstellt.'
            );

            if ($form->get('createWayPoint')->isClicked()) {
                $url = $this->router->generate('update_walk_with_way_point', array('walkId' => $walk->getid()));
            }

            if ($form->get('createWalk')->isClicked()) {
                $url = $this->router->generate('walk_create_form', array('walkId' => $walk->getId()));
            }

            return new RedirectResponse($url);
        }

        return $this->templateEngine->renderResponse(
            ':WayPoint:wayPointForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
