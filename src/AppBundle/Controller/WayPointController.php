<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Walk;
use AppBundle\Entity\WayPoint;
use AppBundle\Form\Type\WayPointType;
use AppBundle\Repository\SystemicQuestionRepositoryInterface;
use AppBundle\Repository\WalkRepositoryInterface;
use AppBundle\Repository\WayPointRepositoryInterface;
use AppBundle\Value\AgeGroup;
use AppBundle\Value\AgeRange;
use AppBundle\Value\Gender;
use AppBundle\Value\PeopleCount;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class WayPointController
{
    private $templateEngine;
    private $wayPointRepository;
    private $router;
    private $formFactory;

    /**
     * @param EngineInterface                     $templateEngine
     * @param FormFactoryInterface                $formFactory
     * @param WayPointRepositoryInterface         $wayPointRepository
     * @param RouterInterface                     $router
     * @param WalkRepositoryInterface             $walkRepository
     * @param SystemicQuestionRepositoryInterface $systemicQuestionRepository
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WayPointRepositoryInterface $wayPointRepository,
        RouterInterface $router,
        WalkRepositoryInterface $walkRepository,
        SystemicQuestionRepositoryInterface $systemicQuestionRepository
    ) {
        $this->templateEngine = $templateEngine;
        $this->wayPointRepository = $wayPointRepository;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->walkRepository = $walkRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
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
     * @Route("waypoint/{wayPointId}", name="way_point_show")
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
     * @Route("table/waypoint", name="data_table_way_point")
     *
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
     * @Route("addWayPointToWalk/{walkId}", name="update_walk_with_way_point")
     *
     * @return Response
     */
    public function updateWalkWithWayPointAction(Walk $walk)
    {
        $wayPoint = WayPoint::fromWalk($walk);

        $form = $this->formFactory->create(
            WayPointType::class,
            $wayPoint,
            array(
                'action' => $this->router->generate('way_point_create', array('walkId' => $walk->getId())),
            )
        );

        return $this->templateEngine->renderResponse(
            ':WayPoint:wayPointForm.html.twig',
            array(
                'walk' => $walk,
                'form' => $form->createView(),
                'wayPoints' => $walk->getWayPoints(),
            )
        );
    }

    /**
     * @param FlashBagInterface $flash
     * @param Walk              $walk
     * @param Request           $request
     *
     * @Route("waypointcreated/{walkId}", name="way_point_create")
     *
     * @return RedirectResponse|Response
     */
    public function createWayPointAction(FlashBagInterface $flash, Walk $walk, Request $request)
    {
        $wayPoint1 = WayPoint::fromWalk($walk);

        $form = $this->formFactory->create(WayPointType::class, $wayPoint1);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var WayPoint $wayPoint */
            $wayPoint = $form->getData();
            $wayPoint->setWalk($walk);
            $this->wayPointRepository->save($wayPoint);

            $flash->add(
                'notice',
                sprintf(
                'Wegpunkt %s wurde erfolgreich zur Runde %s hinzugefügt.',
                    $wayPoint->getLocationName(),
                    $walk->getName()
                )
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
                'walk' => $walk,
            )
        );
    }
}
