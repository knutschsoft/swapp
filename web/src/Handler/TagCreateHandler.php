<?php
declare(strict_types=1);

namespace App\Handler;

use App\Dto\TagCreateRequest;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TagCreateHandler
{
    public function __construct(
        private readonly TagRepository $tagRepository
    ) {
    }

    public function __invoke(TagCreateRequest $request): Tag
    {
        $tag = Tag::fromTagCreateRequest($request);
        $this->tagRepository->save($tag);

        return $tag;
    }
}
