<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class TagCreateController
{
    /** @var TagRepository */
    private $tagRepository;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    public function __construct(
        TagRepository $tagRepository,
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
     *
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
            \sprintf(
                'Tag %s mit der Farbe %s wurde erfolgreich erstellt.',
                $tag->getName(),
                $tag->getColor()
            )
        );

        $url = $this->router->generate('tag_home_screen');
        Assert::notNull($url);

        return new RedirectResponse($url);
    }
}
