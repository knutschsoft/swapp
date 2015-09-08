<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Form\Type\TagType;
use AppBundle\Repository\TagRepository;
use QafooLabs\MVC\Flash;
use QafooLabs\MVC\FormRequest;
use QafooLabs\MVC\RedirectRoute;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TagController
{
    private $tagRepository;

    /**
     * @param TagRepository   $tagRepository
     */
    public function __construct(
        TagRepository $tagRepository
    ) {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @return array
     */
    public function homeScreenAction()
    {
        return [
            'tags' => $this->tagRepository->findAll(),
        ];
    }

    /**
     * @param FormRequest $formRequest
     * @param Flash       $flash
     *
     * @return RedirectResponse|array
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
