<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use AppBundle\Repository\SystemicQuestionRepository;
use AppBundle\Repository\WalkRepository;
use AppBundle\Repository\WayPointRepository;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Routing\RouterInterface;

class WalksController
{
    private $templateEngine;
    private $walkRepository;
    private $router;
    private $formFactory;
    private $wayPointRepository;
    private $userManager;
    private $systemicQuestionRepository;

    /**
     * @param EngineInterface            $templateEngine
     * @param FormFactoryInterface       $formFactory
     * @param WalkRepository             $walkRepository
     * @param RouterInterface            $router
     * @param WayPointRepository         $wayPointRepository
     * @param UserManagerInterface       $userManager
     * @param SystemicQuestionRepository $systemicQuestionRepository
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WalkRepository $walkRepository,
        RouterInterface $router,
        WayPointRepository $wayPointRepository,
        UserManagerInterface $userManager,
        SystemicQuestionRepository $systemicQuestionRepository
    ) {
        $this->formFactory = $formFactory;
        $this->templateEngine = $templateEngine;
        $this->walkRepository = $walkRepository;
        $this->router = $router;
        $this->wayPointRepository = $wayPointRepository;
        $this->userManager = $userManager;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
    }

    /**
     * @param User $user
     *
     * @return Response
     */
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

    /**
     * @param Walk $walk
     *
     * @return Response
     */
    public function showAction(Walk $walk)
    {
        $parameters = [
            'walk' => $walk,
        ];

        return $this->templateEngine->renderResponse(':Walks:show.html.twig', $parameters);
    }

    /**
     * @param Walk $walk
     *
     * @return Response
     */
    public function createWalkFormAction(Walk $walk)
    {
        $form = $this->formFactory->create(
            'app_create_walk',
            $walk,
            array(
                'action' => $this->router->generate('walk_create', array('walkId' => $walk->getId())),
            )
        );

        return $this->templateEngine->renderResponse(
            ':Walks:walkForm.html.twig',
            array(
                'form' => $form->createView(),
                'wayPoints' => $walk->getWayPoints(),
            )
        );
    }

    public function createWalkPrologueFormAction(Team $team)
    {
        // default walk
        // TODO: refactor by move logic outside or something else
        $walk = new Walk();

        $walk->setName("");
        $walk->setStartTime(new \DateTime());
        $walk->setEndTime(new \DateTime());
        $walk->setRating(1);
        $walk->setSystemicAnswer("");
        $walk->setSystemicQuestion($this->systemicQuestionRepository->getRandom()->getQuestion());
        $walk->setWalkReflection("");
        $walk->setWeather("");
        $walk->setIsResubmission(false);
        $walk->setHolidays(false);
        $walk->setCommitments("");
        $walk->setInsights("");
        $walk->setConceptOfDay("");

        $this->walkRepository->save($walk);
        foreach ($team->getUsers() as $user) {
            $user->setWalks([$walk]);
            $this->userManager->updateUser($user);
        }
        $form = $this->formFactory->create(
            'app_create_walk_prologue',
            $walk,
            array(
                'action' => $this->router->generate('walk_start', array('walkId' => $walk->getId())),
            )
        );

        return $this->templateEngine->renderResponse(
            ':Walks:walkPrologueForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function createWalkPrologueAction(Request $request, FlashBag $flashBag, Walk $walk)
    {
        $form = $this->formFactory->create('app_create_walk_prologue', $walk);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $walk = $form->getData();
            $this->walkRepository->update($walk);
            $flashBag->add(
                'notice',
                'Runde wurde erfolgreich gestartet.'
            );

            $url = $this->router->generate('update_walk_with_way_point', array('walkId' => $walk->getId()));

            return new RedirectResponse($url);
        }

        return $this->templateEngine->renderResponse(
            ':Walks:walkPrologueForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param Request  $request
     * @param FlashBag $flashBag
     *
     * @return RedirectResponse|Response
     */
    public function createWalkAction(Request $request, FlashBag $flashBag)
    {
        $form = $this->formFactory->create('app_create_walk', new Walk());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $walk = $form->getData();
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
