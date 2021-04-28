<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class TagListController
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @Route("tag", name="tag_home_screen")
     *
     * @Template(template="tag/homeScreen.html.twig")
     */
    public function __invoke(): array
    {
        return [
            'tags' => $this->tagRepository->findAll(),
        ];
    }
}
