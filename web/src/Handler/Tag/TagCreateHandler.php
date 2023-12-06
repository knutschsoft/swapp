<?php
declare(strict_types=1);

namespace App\Handler\Tag;

use App\Dto\Tag\TagCreateRequest;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class TagCreateHandler
{
    public function __construct(
        private TagRepository $tagRepository
    ) {
    }

    public function __invoke(TagCreateRequest $request): Tag
    {
        $tag = Tag::fromTagCreateRequest($request);
        $this->tagRepository->save($tag);

        return $tag;
    }
}
