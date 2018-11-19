<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Form\Type\TeamType;
use AppBundle\Repository\TeamRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class TeamFormController
{
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var TeamRepositoryInterface */
    private $teamRepository;
    /** @var RouterInterface */
    private $router;

    public function __construct(FormFactoryInterface $formFactory, TeamRepositoryInterface $teamRepository, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->teamRepository = $teamRepository;
        $this->router = $router;
    }

    /**
     * @Route("/team/form-{id}", name="team_form", requirements={"id"="\d+"}, defaults={"id"=""})
     * @Template(template="team/form.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function __invoke(Request $request, FlashBagInterface $flash, ?Team $team = null)
    {
        $form = $this->formFactory->create(TeamType::class, $team);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return [
                'form' => $form->createView(),
            ];
        }

        /** @var Team $data */
        $data = $form->getData();
        $this->teamRepository->save($data);

        $flash->add('notice', 'Team erfolgreich erstellt/bearbeitet.');

        return new RedirectResponse($this->router->generate('team_list'));
    }
}