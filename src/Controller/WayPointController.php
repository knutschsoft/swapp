<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Walk;
use App\Entity\WayPoint;
use App\Form\Type\WayPointType;
use App\Repository\WayPointRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class WayPointController extends AbstractController
{
    /** @var EngineInterface */
    private $templateEngine;
    /** @var WayPointRepository */
    private $wayPointRepository;
    /** @var RouterInterface */
    private $router;
    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WayPointRepository $wayPointRepository,
        RouterInterface $router
    ) {
        $this->templateEngine = $templateEngine;
        $this->wayPointRepository = $wayPointRepository;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @todo needed?
     */
    public function homeScreenAction(): Response
    {
        return $this->templateEngine->renderResponse('way_point/wayPointForm.html.twig');
    }

    /**
     * @Route("waypoint/{wayPointId}", name="way_point_show")
     */
    public function showAction(WayPoint $wayPoint): Response
    {
        $parameters = [
            'wayPoint' => $wayPoint,
        ];

        return $this->templateEngine->renderResponse('way_point/show.html.twig', $parameters);
    }

    /**
     * @Route("table/waypoint", name="data_table_way_point")
     */
    public function dataTableAction(): Response
    {
        $parameters = [
            'wayPoints' => $this->wayPointRepository->findAll(),
        ];

        return $this->templateEngine->renderResponse('way_point/dataTable.html.twig', $parameters);
    }

    /**
     * @Route("form/addWayPointToWalk/{walkId}", name="update_walk_with_way_point")
     */
    public function updateWalkWithWayPointAction(Walk $walk): Response
    {
        $wayPoint = WayPoint::fromWalk($walk);

        $form = $this->formFactory->create(
            WayPointType::class,
            $wayPoint,
            [
                'action' => $this->router->generate('way_point_create', ['walkId' => $walk->getId()]),
            ]
        );

        return $this->json([
            'form' => $this->renderView(
                'way_point/wayPointForm.html.twig',
                [
                    'walk' => $walk,
                    'form' => $form->createView(),
                    'wayPoints' => $walk->getWayPoints(),
                ]
            ),
        ]);
    }

    /**
     * @Route("form/waypointcreated/{walkId}", name="way_point_create")
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
                \sprintf(
                    'Wegpunkt %s wurde erfolgreich zur Runde %s hinzugefügt.',
                    $wayPoint->getLocationName(),
                    $walk->getName()
                )
            );

            return new JsonResponse();
        }

        return $this->json([
            'form' => $this->renderView(
                'way_point/wayPointForm.html.twig',
                [
                    'form' => $form->createView(),
                    'wayPoints' => $walk->getWayPoints(),
                    'walk' => $walk,
                ]
            ),
        ]);
    }
}
