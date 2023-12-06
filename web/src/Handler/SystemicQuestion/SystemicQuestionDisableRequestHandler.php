<?php
declare(strict_types=1);

namespace App\Handler\SystemicQuestion;

use App\Dto\SystemicQuestion\SystemicQuestionDisableRequest;
use App\Entity\SystemicQuestion;
use App\Repository\SystemicQuestionRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SystemicQuestionDisableRequestHandler
{
    public function __construct(
        private readonly SystemicQuestionRepository $systemicQuestionRepository
    ) {
    }

    public function __invoke(SystemicQuestionDisableRequest $request): SystemicQuestion
    {
        $systemicQuestion = $request->systemicQuestion;
        $systemicQuestion->disable();
        $this->systemicQuestionRepository->save($systemicQuestion);

        return $systemicQuestion;
    }
}
