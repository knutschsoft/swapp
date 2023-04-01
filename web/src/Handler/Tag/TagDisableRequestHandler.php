<?php
declare(strict_types=1);

namespace App\Handler\Tag;

use App\Dto\Tag\TagDisableRequest;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TagDisableRequestHandler
{
    public function __construct(
        private readonly TagRepository $tagRepository
    ) {
    }

    public function __invoke(TagDisableRequest $request): Tag
    {
        $tag = $request->tag;
        $tag->disable();
        $this->tagRepository->save($request->tag);

        return $request->tag;
    }
}
