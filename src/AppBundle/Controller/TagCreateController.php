<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Form\Type\TagType;
use AppBundle\Repository\TagRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class TagCreateController
{
    /** @var TagRepositoryInterface */
    private $tagRepository;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var RouterInterface */
    private $router;

    public function __construct(
        TagRepositoryInterface $tagRepository,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        $this->tagRepository = $tagRepository;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param Request           $request
     * @param FlashBagInterface $flash
     *
     * @Route("createtag", name="tag_create")
     * @Template("tag/createTag.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function __invoke(Request $request, FlashBagInterface $flash)
    {
        $form = $this->formFactory->create(TagType::class, new Tag());
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return ['form' => $form->createView()];
        }

        /** @var Tag $tag */
        $tag = $form->getData();

        $this->tagRepository->save($tag);
        $flash->add(
            'notice',
            sprintf(
                'Tag %s mit der Farbe %s wurde erfolgreich erstellt.',
                $tag->getName(),
                $tag->getColor()
            )
        );

        return new RedirectResponse($this->router->generate('tag_home_screen'));
    }
}