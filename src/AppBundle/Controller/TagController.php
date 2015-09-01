<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Repository\TagRepository;
use QafooLabs\MVC\Flash;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class TagController extends Controller
{
    private $templateEngine;
    private $tagRepository;
    private $router;
    private $formFactory;

    /**
     * @param EngineInterface      $templateEngine
     * @param FormFactoryInterface $formFactory
     * @param TagRepository        $tagRepository
     * @param RouterInterface      $router
     */
    public function __construct(
        EngineInterface $templateEngine,
        FormFactoryInterface $formFactory,
        TagRepository $tagRepository,
        RouterInterface $router
    ) {
        $this->formFactory = $formFactory;
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
     * @return Response
     */
    public function createTagFormAction()
    {
        $tag = new Tag();
        $form = $this->formFactory->create(
            'app_create_tag',
            $tag,
            array(
                'action' => $this->router->generate('tag_create'),
            )
        );

        return $this->templateEngine->renderResponse(
            ':Tags:tagForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @param Request $request
     * @param Flash   $flash
     *
     * @return RedirectResponse|Response
     */
    public function createTagAction(Request $request, Flash $flash)
    {
        $form = $this->formFactory->create('app_create_tag', new Tag());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tag = $form->getData();
            $this->tagRepository->save($tag);
            $flash->add(
                'notice',
                'Tag wurde erfolgreich erstellt.'
            );

            $url = $this->router->generate('tag_home_screen');

            return new RedirectResponse($url);
        }

        return $this->templateEngine->renderResponse(
            ':Tags:tagForm.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
