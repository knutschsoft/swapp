<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Team;
use App\Form\Type\TeamType;
use App\Repository\TeamRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class TeamFormController
{
    private FormFactoryInterface $formFactory;

    private TeamRepository $teamRepository;

    private RouterInterface $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        TeamRepository $teamRepository,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->teamRepository = $teamRepository;
        $this->router = $router;
    }

    /**
     * @Route("/team/form-{id}", name="team_form", requirements={"id"="\d+"}, defaults={"id"=""})
     *
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

        $team = $form->getData();
        \assert($team instanceof Team);
        $this->teamRepository->save($team);

        $flash->add('notice', 'Team erfolgreich erstellt/bearbeitet.');

        $url = $this->router->generate('team_list');
        Assert::notNull($url);

        return new RedirectResponse($url);
    }
}
