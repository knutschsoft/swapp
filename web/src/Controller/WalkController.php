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

    /**
     * @param WalkRepository             $walkRepository
     * @param RouterInterface            $router
     * @param FormFactoryInterface       $formFactory
     * @param UserRepository             $userRepository
     * @param SystemicQuestionRepository $systemicQuestionRepository
     */
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
     * @param Walk    $walk
     * @param Request $request
     *
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
     * @param Walk    $walk
     * @param Request $request
     *
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
