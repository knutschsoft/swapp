<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
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

    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':Tags:tagsHomeScreen.html.twig');
    }

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

    public function createTagAction(Request $request, FlashBag $flashBag)
    {
        $form = $this->formFactory->create('app_create_tag', new Tag());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tag = $form->getData();
            $this->tagRepository->save($tag);
            $flashBag->add(
                'notice',
                'Tag wurde erfolgreich erstellt.'
            );

            $url = $this->router->generate('tag_homeScreen');

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
