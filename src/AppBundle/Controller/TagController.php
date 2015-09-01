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
    private $templateEngine;
    private $tagRepository;
    private $router;

    /**
     * @param EngineInterface      $templateEngine
     * @param TagRepository        $tagRepository
     * @param RouterInterface      $router
     */
    public function __construct(
        EngineInterface $templateEngine,
        TagRepository $tagRepository,
        RouterInterface $router
    ) {
        $this->templateEngine = $templateEngine;
        $this->tagRepository = $tagRepository;
        $this->router = $router;
    }

    /**
     * @return Response
     */
    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':Tags:tagsHomeScreen.html.twig');
    }

    /**
     * @param FormRequest $formRequest
     *
     * @return Response
     */
    public function createTagFormAction(FormRequest $formRequest)
    {
        $formRequest->handle(
            new TagType(),
            new Tag(),
            array(
                'action' => $this->router->generate('tag_create'),
            )
        );

        return $this->templateEngine->renderResponse(
            ':Tags:tagForm.html.twig',
            array(
                'form' => $formRequest->createFormView(),
            )
        );
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
            return $this->templateEngine->renderResponse(
                ':Tags:tagForm.html.twig',
                array(
                    'form' => $formRequest->createFormView(),
                )
            );
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
