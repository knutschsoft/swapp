<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Form\Type\TagType;
use AppBundle\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class TagController extends Controller
{
    private $templateEngine;
    private $tagRepository;

    /**
     * @param EngineInterface $templateEngine
     * @param TagRepository $tagRepository
     */
    public function __construct(EngineInterface $templateEngine, TagRepository $tagRepository, ContainerInterface $container)
    {
        $this->templateEngine = $templateEngine;
        $this->tagRepository = $tagRepository;
        $this->container = $container;
    }

    public function homeScreenAction()
    {
        return $this->templateEngine->renderResponse(':Tags:tagsHomeScreen.html.twig');
    }

    public function createTagForm()
    {
        $tag = new Tag();
        $form = $this->createForm(new TagType(), $tag, array(
            'action' => $this->generateUrl('tag_create'),
        ));

        return $this->templateEngine->renderResponse(':Tags:tagForm.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function createTag(Request $request)
    {
        $form = $this->createForm(new TagType(), new Tag());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $tag = $form->getData();
            $this->tagRepository->save($tag);

            return $this->redirectToRoute('tag_testscreen');
        }

        return $this->templateEngine->renderResponse(':Tags:tagForm.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
