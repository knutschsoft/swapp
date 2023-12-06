<?php
declare(strict_types=1);

namespace App\Handler\SystemicQuestion;

use App\Dto\SystemicQuestion\SystemicQuestionCreateRequest;
use App\Entity\SystemicQuestion;
use App\Repository\SystemicQuestionRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SystemicQuestionCreateRequestHandler
{
    public function __construct(
        private readonly SystemicQuestionRepository $systemicQuestionRepository
    ) {
    }

    public function __invoke(SystemicQuestionCreateRequest $request): SystemicQuestion
    {
        $systemicQuestion = SystemicQuestion::fromString($request->question, $request->client);
        $this->systemicQuestionRepository->save($systemicQuestion);

        return $systemicQuestion;
    }
}
