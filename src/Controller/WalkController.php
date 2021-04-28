<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Walk;
use App\Form\Type\WalkPrologueType;
use App\Form\Type\WalkType;
use App\Repository\SystemicQuestionRepository;
use App\Repository\UserRepository;
use App\Repository\WalkRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class WalkController extends AbstractController
{
    /** @var WalkRepository */
    private $walkRepository;
    /** @var RouterInterface */
    private $router;
    /** @var UserRepository */
    private $userRepository;
    /** @var SystemicQuestionRepository */
    private $systemicQuestionRepository;
    /** @var FormFactoryInterface */
    private $formFactory;

    public function __construct(
        WalkRepository $walkRepository,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        SystemicQuestionRepository $systemicQuestionRepository
    ) {
        $this->walkRepository = $walkRepository;
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @Route("walks", name="walk_home_screen")
     *
     * @Template(template="walk/homeScreen.html.twig")
     */
    public function homeScreenAction(User $user, Request $request): array
    {
        $order = $request->query->get('order', 'startTime');
        $sort = $request->query->get('sort', 'desc');

        $walks = $this->walkRepository->findAllOrderBy('walk.'.$order, $sort);
        $unfinishedWalks = $this->walkRepository->findAllUnfinishedByUser($user);
        $teams = $user->getTeams();

        return [
            'unfinishedWalks' => $unfinishedWalks,
            'walks' => $walks,
            'teams' => $teams,
            'order' => $order,
            'sort' => $sort,
        ];
    }

    /**
     * @Route("walk/{walkId}", name="walk_show")
     *
     * @Template(template="walk/show.html.twig")
     */
    public function showAction(Walk $walk): array
    {
        return [
            'walk' => $walk,
        ];
    }

    /**
     * @Route("createwalk/{walkId}", name="walk_create_form")
     *
     * @Template(template="walk/createWalkForm.html.twig")
     */
    public function createWalkFormAction(Walk $walk, Request $request): array
    {
        $form = $this->formFactory->create(
            WalkType::class,
            $walk,
            [
                'action' => $this->router->generate('walk_create', ['walkId' => $walk->getId()]),
            ]
        );
        $form->handleRequest($request);

        return [
            'walk' => $walk,
            'form' => $form->createView(),
            'wayPoints' => $walk->getWayPoints(),
        ];
    }

    /**
     * @Route("/form/walk-prologue/{walkId}", name="walk_start")
     *
     * @return JsonResponse
     */
    public function createWalkPrologueAction(Walk $walk, Request $request)
    {
        $form = $this->formFactory->create(WalkPrologueType::class, $walk);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $view = $this->renderView('walk/createWalkPrologueForm.html.twig', ['form' => $form->createView()]);

            return $this->json(
                [
                    'form' => $view,
                ]
            );
        }

        $walk = $form->getData();
        $this->walkRepository->update($walk);

        return new JsonResponse();
    }

    /**
     * @Route("form/walk-epilogue/{walkId}", name="walk_create")
     *
     * @return JsonResponse
     */
    public function createWalkAction(Walk $walk, Request $request)
    {
        $form = $this->formFactory->create(WalkType::class, $walk);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $view = $this->renderView(
                'walk/createWalkForm.html.twig',
                [
                    'walk' => $walk,
                    'form' => $form->createView(),
                ]
            );

            return $this->json(
                [
                    'form' => $view,
                ]
            );
        }

        $walk = $form->getData();
        $this->walkRepository->update($walk);

        return new JsonResponse();
    }
}
