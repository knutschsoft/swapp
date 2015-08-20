<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Walk;
use AppBundle\Entity\WayPoint;
use AppBundle\Repository\SystemicQuestionRepository;
use AppBundle\Repository\TagRepository;
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
    private $tagRepository;

    /**
     * @param EngineInterface            $templateEngine
     * @param FormFactoryInterface       $formFactory
     * @param WayPointRepository         $wayPointRepository
     * @param RouterInterface            $router
     * @param WalkRepository             $walkRepository
     * @param SystemicQuestionRepository $systemicQuestionRepository
     * @param UserManagerInterface       $userManager
     * @param TagRepository              $tagRepository
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        WayPointRepository $wayPointRepository,
        RouterInterface $router,
        WalkRepository $walkRepository,
        SystemicQuestionRepository $systemicQuestionRepository,
        UserManagerInterface $userManager,
        TagRepository $tagRepository
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':WayPoint:wayPointForm.html.twig');
    }

    /**
     * @param WayPoint $wayPoint
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(WayPoint $wayPoint)
    {
        $parameters = [
            'wayPoint' => $wayPoint,
        ];

        return $this->templateEngine->renderResponse('WayPoint/show.html.twig', $parameters);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dataTableAction()
    {
        $parameters = [
            'wayPoints' => $this->wayPointRepository->findAll()
        ];

        return $this->templateEngine->renderResponse('WayPoint/dataTable.html.twig', $parameters);
    }

    /**
     * @param Walk $walk
     *
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @param Request  $request
     * @param FlashBag $flashBag
     * @param Walk     $walk
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createWayPointAction(Request $request, FlashBag $flashBag, Walk $walk)
    {
        $form = $this->formFactory->create('app_create_way_point', new WayPoint());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $wayPoint = $form->getData();
            $wayPoint->setWalk($walk);
            $this->wayPointRepository->save($wayPoint);

            foreach ($wayPoint->getWayPointTags() as $tag) {
                $array = $tag->getWayPoints()->getValues();
                array_push($array, $wayPoint);
                $tag->setWayPoints($array);
                $this->tagRepository->save($tag);
            }

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
