<?php
declare(strict_types=1);

namespace App\Handler\SystemicQuestion;

use App\Dto\SystemicQuestion\SystemicQuestionEnableRequest;
use App\Entity\SystemicQuestion;
use App\Repository\SystemicQuestionRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SystemicQuestionEnableRequestHandler
{
    public function __construct(
        private readonly SystemicQuestionRepository $systemicQuestionRepository
    ) {
    }

    public function __invoke(SystemicQuestionEnableRequest $request): SystemicQuestion
    {
        $systemicQuestion = $request->systemicQuestion;
        $systemicQuestion->enable();
        $this->systemicQuestionRepository->save($systemicQuestion);

        return $systemicQuestion;
    }
}
