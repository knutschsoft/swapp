<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Walk;
use AppBundle\Entity\WayPoint;
use AppBundle\Repository\SystemicQuestionRepositoryInterface;
use AppBundle\Repository\TagRepositoryInterface;
use AppBundle\Repository\WalkRepositoryInterface;
use AppBundle\Repository\WayPointRepositoryInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use QafooLabs\MVC\Flash;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class WayPointController
{
    private $templateEngine;
    private $wayPointRepository;
    private $router;
    private $formFactory;
    private $userManager;
    private $tagRepository;

    /**
     * @param EngineInterface                     $templateEngine
     * @param FormFactoryInterface                $formFactory
     * @param WayPointRepositoryInterface         $wayPointRepository
     * @param RouterInterface                     $router
     * @param WalkRepositoryInterface             $walkRepository
     * @param SystemicQuestionRepositoryInterface $systemicQuestionRepository
     * @param UserManagerInterface                $userManager
     * @param TagRepositoryInterface              $tagRepository
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WayPointRepositoryInterface $wayPointRepository,
        RouterInterface $router,
        WalkRepositoryInterface $walkRepository,
        SystemicQuestionRepositoryInterface $systemicQuestionRepository,
        UserManagerInterface $userManager,
        TagRepositoryInterface $tagRepository
    ) {
        $this->templateEngine = $templateEngine;
        $this->wayPointRepository = $wayPointRepository;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->walkRepository = $walkRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
        $this->userManager = $userManager;
        $this->tagRepository = $tagRepository;
    }

    /**
     * @todo needed?
     * @return Response
     */
    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':WayPoint:wayPointForm.html.twig');
    }

    /**
     * @param WayPoint $wayPoint
     *
     * @return Response
     */
    public function showAction(WayPoint $wayPoint)
    {
        $parameters = [
            'wayPoint' => $wayPoint,
        ];

        return $this->templateEngine->renderResponse('WayPoint/show.html.twig', $parameters);
    }

    /**
     * @return Response
     */
    public function dataTableAction()
    {
        $parameters = [
            'wayPoints' => $this->wayPointRepository->findAll(),
        ];

        return $this->templateEngine->renderResponse('WayPoint/dataTable.html.twig', $parameters);
    }

    /**
     * @param Walk $walk
     *
     * @return Response
     */
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
                'wayPoints' => $walk->getWayPoints(),
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
    public function createWayPointAction(Request $request, Flash $flash, Walk $walk)
    {
        $form = $this->formFactory->create('app_create_way_point', new WayPoint());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $wayPoint = $form->getData();
            $wayPoint->setWalk($walk);
            $this->wayPointRepository->save($wayPoint);

            $flash->add(
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
                'wayPoints' => $walk->getWayPoints(),
            )
        );
    }
}
