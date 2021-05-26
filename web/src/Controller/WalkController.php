<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Team;
use App\Entity\Walk;
use App\Form\Type\WalkPrologueType;
use App\Form\Type\WalkType;
use App\Repository\SystemicQuestionRepository;
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
    private WalkRepository $walkRepository;
    private RouterInterface $router;
    private FormFactoryInterface $formFactory;
    private SystemicQuestionRepository $systemicQuestionRepository;

    public function __construct(
        WalkRepository $walkRepository,
        SystemicQuestionRepository $systemicQuestionRepository,
        RouterInterface $router,
        FormFactoryInterface $formFactory
    ) {
        $this->walkRepository = $walkRepository;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
    }

    /**
     * @param Walk    $walk
     * @param Request $request
     *
     * @Route("createwalk/{walkId}", name="walk_create_form")
     *
     * @Template(template="walk/createWalkForm.html.twig")
     *
     * @return array<string,mixed>
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
     * @param Team    $team
     * @param Request $request
     *
     * @Route("/form/walk-prologue/{teamId}", name="walk_start")
     *
     * @return JsonResponse
     */
    public function createWalkPrologueAction(Team $team, Request $request): JsonResponse
    {
        $systemicQuestion = $this->systemicQuestionRepository->getRandom();
        $form = $this->formFactory->create(WalkPrologueType::class, Walk::prologue($team, $systemicQuestion));
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $view = $this->renderView('walk/createWalkPrologueForm.html.twig', ['form' => $form->createView()]);

            return $this->json(
                [
                    'form' => $view,
                ]
            );
        }

        /** @var Walk $walk */
        $walk = $form->getData();
        $this->walkRepository->save($walk);

        return new JsonResponse(['walkId' => $walk->getId()]);
    }

    /**
     * @param Walk    $walk
     * @param Request $request
     *
     * @Route("form/walk-epilogue/{walkId}", name="walk_create")
     *
     * @return JsonResponse
     */
    public function createWalkAction(Walk $walk, Request $request): JsonResponse
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
