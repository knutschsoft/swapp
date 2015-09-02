<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Form\Type\TagType;
use AppBundle\Repository\TagRepository;
use QafooLabs\MVC\Flash;
use QafooLabs\MVC\FormRequest;
use QafooLabs\MVC\RedirectRoute;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TagController
{
    private $tagRepository;
    private $router;
    private $templateEngine;

    /**
     * @param EngineInterface $templateEngine
     * @param TagRepository   $tagRepository
     * @param RouterInterface $router
     */
    public function __construct(
        EngineInterface $templateEngine,
        TagRepository $tagRepository,
        RouterInterface $router
    ) {
        $this->tagRepository = $tagRepository;
        $this->router = $router;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @return Response
     */
    public function homeScreenAction()
    {
        $parameters = [
            'tags' => $this->tagRepository->findAll(),
        ];

        return $this->templateEngine->renderResponse(':Tag:homeScreen.html.twig', $parameters);
    }

    /**
     * @param FormRequest $formRequest
     *
     * @return Response
     */
    public function createTagFormAction(FormRequest $formRequest)
    {
        $parameters = [
            'tags' => $this->tagRepository->findAll(),
        ];
        $formRequest->handle(
            new TagType(),
            new Tag(),
            array(
                'action' => $this->router->generate('tag_create', $parameters),
            )
        );

        return ['form' => $formRequest->createFormView()];
    }

    /**
     * @param FormRequest $formRequest
     * @param Flash       $flash
     *
     * @return RedirectResponse|Response
     */
    public function createTagAction(FormRequest $formRequest, Flash $flash)
    {
        if (!$formRequest->handle(new TagType(), new Tag())) {
            return ['form' => $formRequest->createFormView()];
        }

        $tag = $formRequest->getValidData();

        $this->tagRepository->save($tag);
        $flash->add(
            'notice',
            'Tag wurde erfolgreich erstellt.'
        );

        return new RedirectRoute('tag_home_screen');
    }
}
