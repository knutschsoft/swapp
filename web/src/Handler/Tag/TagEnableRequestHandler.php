<?php
declare(strict_types=1);

namespace App\Handler\Tag;

use App\Dto\Tag\TagEnableRequest;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class TagEnableRequestHandler
{
    public function __construct(
        private TagRepository $tagRepository
    ) {
    }

    public function __invoke(TagEnableRequest $request): Tag
    {
        $tag = $request->tag;
        $tag->enable();
        $this->tagRepository->save($request->tag);

        return $request->tag;
    }
}
