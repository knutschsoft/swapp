<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use AppBundle\Entity\Walk;
use AppBundle\Form\Type\WalkPrologueType;
use AppBundle\Form\Type\WalkType;
use AppBundle\Repository\SystemicQuestionRepositoryInterface;
use AppBundle\Repository\TagRepositoryInterface;
use AppBundle\Repository\WalkRepositoryInterface;
use AppBundle\Repository\WayPointRepositoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use QafooLabs\MVC\Flash;
use QafooLabs\MVC\FormRequest;
use QafooLabs\MVC\RedirectRoute;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\RouterInterface;

class WalkController
{
    private $walkRepository;
    private $router;
    private $wayPointRepository;
    private $userManager;
    private $systemicQuestionRepository;
    private $tagRepository;

    /**
     * @param WalkRepositoryInterface             $walkRepository
     * @param RouterInterface                     $router
     * @param WayPointRepositoryInterface         $wayPointRepository
     * @param UserManagerInterface                $userManager
     * @param SystemicQuestionRepositoryInterface $systemicQuestionRepository
     * @param TagRepositoryInterface              $tagRepository
     */
    public function __construct(
        WalkRepositoryInterface $walkRepository,
        RouterInterface $router,
        WayPointRepositoryInterface $wayPointRepository,
        UserManagerInterface $userManager,
        SystemicQuestionRepositoryInterface $systemicQuestionRepository,
        TagRepositoryInterface $tagRepository
    ) {
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
     * @return array
     */
    public function homeScreenAction(User $user, Request $request)
    {
        $order = $request->query->get('order', 'startTime');
        $sort = $request->query->get('sort', 'asc');

        $walks = $this->walkRepository->findAllOrderBy('walk.' . $order, $sort);
        $teams = $user->getTeams();
        return [
            'walks' => $walks,
            'teams' => $teams,
            'order' => $order,
            'sort' => $sort,
        ];
    }

    /**
     * @param Walk $walk
     *
     * @return array
     */
    public function showAction(Walk $walk)
    {
        return [
            'walk' => $walk,
        ];
    }

    /**
     * @param Walk        $walk
     * @param FormRequest $formRequest
     *
     * @return array
     */
    public function createWalkFormAction(Walk $walk, FormRequest $formRequest)
    {
        $formRequest->handle(
            WalkType::class,
            $walk,
            [
                'action' => $this->router->generate('walk_create', array('walkId' => $walk->getId())),
            ]
        );

        return [
            'form' => $formRequest->createFormView(),
            'wayPoints' => $walk->getWayPoints(),
        ];
    }

    /**
     * @param Team        $team
     * @param FormRequest $formRequest
     *
     * @return array
     */
    public function createWalkPrologueFormAction(Team $team, FormRequest $formRequest)
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
        $walk->setSystemicQuestion($this->systemicQuestionRepository->getRandom());
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

        $formRequest->handle(
            WalkPrologueType::class,
            $walk,
            [
                'action' => $this->router->generate('walk_start', array('walkId' => $walk->getId())),
            ]
        );

        return [
            'form' => $formRequest->createFormView(),
        ];
    }

    /**
     * @param Flash       $flash
     * @param Walk        $walk
     * @param FormRequest $formRequest
     *
     * @return array|RedirectRoute
     */
    public function createWalkPrologueAction(Flash $flash, Walk $walk, FormRequest $formRequest)
    {
        if (!$formRequest->handle(
            WalkPrologueType::class,
            $walk
        )) {

            return [
                'form' => $formRequest->createFormView(),
            ];
        }

        $walk = $formRequest->getValidData();
        $this->walkRepository->update($walk);
        $flash->add(
            'notice',
            'Runde wurde erfolgreich gestartet.'
        );

        return new RedirectRoute('update_walk_with_way_point', ['walkId' => $walk->getId()]);
    }

    /**
     * @param Flash       $flash
     * @param Walk        $walk
     * @param FormRequest $formRequest
     *
     * @return array|RedirectRoute
     */
    public function createWalkAction(Flash $flash, Walk $walk, FormRequest $formRequest)
    {
        if (!$formRequest->handle(WalkType::class, $walk)) {

            return [
                'form' => $formRequest->createFormView(),
            ];
        }

        $walk = $formRequest->getValidData();
        $this->walkRepository->update($walk);

        $flash->add(
            'notice',
            'Runde wurde erfolgreich erstellt.'
        );

        return new RedirectRoute('walk_home_screen');
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
                    'angetroffene Männer',
                    'angetroffene Frauen',
                    'Teamname',
//                    'TeamMitglieder',
//                    'Gäste',
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
