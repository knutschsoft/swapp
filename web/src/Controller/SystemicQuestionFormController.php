<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\SystemicQuestion;
use App\Form\Type\SystemicQuestionType;
use App\Repository\SystemicQuestionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class SystemicQuestionFormController
{
    private FormFactoryInterface $formFactory;

    private SystemicQuestionRepository $systemicQuestionRepository;

    private RouterInterface $router;

    public function __construct(
        FormFactoryInterface $formFactory,
        SystemicQuestionRepository $systemicQuestionRepository,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
        $this->systemicQuestionRepository = $systemicQuestionRepository;
        $this->router = $router;
    }

    /**
     * @Route("/systemic-question/form-{id}", name="systemic_question_form", requirements={"id"="\d+"}, defaults={"id"=""})
     *
     * @Template(template="systemic_question/form.html.twig")
     *
     * @param Request               $request
     * @param FlashBagInterface     $flash
     * @param SystemicQuestion|null $systemicQuestion
     *
     * @return array<string,mixed>|RedirectResponse
     */
    public function __invoke(Request $request, FlashBagInterface $flash, ?SystemicQuestion $systemicQuestion = null)
    {
        $isCreateNew = (bool) !$systemicQuestion;
        $form = $this->formFactory->create(SystemicQuestionType::class, $systemicQuestion);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return [
                'form' => $form->createView(),
                'isCreateNew' => $isCreateNew,
            ];
        }

        $systemicQuestion = $form->getData();
        \assert($systemicQuestion instanceof SystemicQuestion);
        $this->systemicQuestionRepository->save($systemicQuestion);

        if ($isCreateNew) {
            $flash->add('notice', 'Systemische Frage erfolgreich erstellt.');
        } else {
            $flash->add('notice', 'Systemische Frage erfolgreich bearbeitet.');
        }

        $url = $this->router->generate('systemic_question_list');
        Assert::notNull($url);

        return new RedirectResponse($url);
    }
}
