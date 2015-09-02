<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use AppBundle\Repository\SystemicQuestionRepository;
use AppBundle\Repository\TagRepository;
use AppBundle\Repository\WalkRepository;
use AppBundle\Repository\WayPointRepository;
use FOS\UserBundle\Model\UserManagerInterface;
use QafooLabs\MVC\Flash;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    private $tagRepository;

    /**
     * @param EngineInterface            $templateEngine
     * @param FormFactoryInterface       $formFactory
     * @param WalkRepository             $walkRepository
     * @param RouterInterface            $router
     * @param WayPointRepository         $wayPointRepository
     * @param UserManagerInterface       $userManager
     * @param SystemicQuestionRepository $systemicQuestionRepository
     * @param TagRepository              $tagRepository
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WalkRepository $walkRepository,
        RouterInterface $router,
        WayPointRepository $wayPointRepository,
        UserManagerInterface $userManager,
        SystemicQuestionRepository $systemicQuestionRepository,
        TagRepository $tagRepository
    ) {
        $this->formFactory = $formFactory;
        $this->templateEngine = $templateEngine;
        $this->walkRepository = $walkRepository;
        $this->router = $router;
        $this->wayPointRepository = $wayPointRepository;
        $this->userManager = $userManager;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
        $this->tagRepository = $tagRepository;
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

    /**
     * @param Team $team
     *
     * @return Response
     */
    public function createWalkPrologueFormAction(Team $team)
    {
        // default walk
        // TODO: refactor by move logic outside or something else
        $walk = new Walk();

        $walk->setTeamName($team->getName());
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

    /**
     * @param Request $request
     * @param Flash   $flash
     * @param Walk    $walk
     *
     * @return RedirectResponse|Response
     */
    public function createWalkPrologueAction(Request $request, Flash $flash, Walk $walk)
    {
        $form = $this->formFactory->create('app_create_walk_prologue', $walk);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $walk = $form->getData();
            $this->walkRepository->update($walk);
            $flash->add(
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
     * @param Request $request
     * @param Flash   $flash
     * @param Walk    $walk
     *
     * @return RedirectResponse|Response
     */
    public function createWalkAction(Request $request, Flash $flash, Walk $walk)
    {
        $form = $this->formFactory->create('app_create_walk', $walk);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $walk = $form->getData();
            $this->walkRepository->update($walk);

            $flash->add(
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

    /**
     * @return StreamedResponse
     */
    public function exportAction()
    {
        // get the service container to pass to the closure
        $walkRepository = $this->walkRepository;

        $response = new StreamedResponse(
            function () use ($walkRepository) {

                // The getExportQuery method returns a query that is used to retrieve
                // all the objects (lines of your csv file) you need. The iterate method
                // is used to limit the memory consumption
                $results = $walkRepository->getFindAllQuery()->iterate();
                $handle = fopen('php://output', 'r+');

                $header = [
                    'Id',
                    'Name',
                    'Beginn',
                    'Ende',
                    'Reflexion',
                    'Bewertung',
                    'systemische Frage',
                    'systemische Antwort',
                    'Erkenntnisse, Überlegungen, Zielsettungen',
                    'Termine, Besorgungen, Verabredungen',
                    'Wiedervorlage Dienstberatung',
                    'Wetter',
                    'Ferien',
                    'Tageskonzept',
                ];

                fputcsv($handle, $header);

                while (false !== ($row = $results->next())) {
                    // add a line in the csv file. You need to implement a toArray() method
                    // to transform your object into an array
//                dump($row[0]->toArray());
                    fputcsv($handle, $row[0]->toArray());
                    // used to limit the memory consumption
//                $em->detach($row[0]);
                }

                fclose($handle);
            }
        );

        $response->headers->set('Content-Type', 'application/force-download');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }
}
