<?php
declare(strict_types=1);

namespace App\Handler\SystemicQuestion;

use App\Dto\SystemicQuestion\SystemicQuestionChangeRequest;
use App\Entity\SystemicQuestion;
use App\Repository\SystemicQuestionRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SystemicQuestionChangeHandler implements MessageHandlerInterface
{
    private SystemicQuestionRepository $systemicQuestionRepository;

    public function __construct(
        SystemicQuestionRepository $systemicQuestionRepository
    ) {
        $this->systemicQuestionRepository = $systemicQuestionRepository;
    }

    public function __invoke(SystemicQuestionChangeRequest $request): SystemicQuestion
    {
        $systemicQuestion = $request->systemicQuestion;
        $systemicQuestion->updateClient($request->client);
        $systemicQuestion->setQuestion($request->question);
        $this->systemicQuestionRepository->save($systemicQuestion);

        return $systemicQuestion;
    }
}
