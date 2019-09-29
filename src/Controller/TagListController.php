<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class TagListController
{
    /** @var TagRepository */
    private $tagRepository;

    public function __construct(
        TagRepository $tagRepository
    ) {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @Route("tag", name="tag_home_screen")
     *
     * @Template(template="tag/homeScreen.html.twig")
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'tags' => $this->tagRepository->findAll(),
        ];
    }
}
