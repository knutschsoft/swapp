<?php
declare(strict_types=1);

namespace App\Controller;

use App\Query\FindAllSystemicQuestions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class SystemicQuestionListController
{
    private FindAllSystemicQuestions $findAllSystemicQuestions;

    public function __construct(FindAllSystemicQuestions $findAllTeams)
    {
        $this->findAllSystemicQuestions = $findAllTeams;
    }

    /**
     * @Route("systemic-question/list", name="systemic_question_list")
     *
     * @Template(template="systemic_question/list.html.twig")
     */
    public function __invoke(): array
    {
        return [
            'systemicQuestions' => $this->findAllSystemicQuestions->__invoke(),
        ];
    }
}
