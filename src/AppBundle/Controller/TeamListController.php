<?php

namespace AppBundle\Controller;

use AppBundle\Query\FindAllTeams;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class TeamListController
{
    /** @var FindAllTeams */
    private $findAllTeams;

    public function __construct(FindAllTeams $findAllTeams)
    {
        $this->findAllTeams = $findAllTeams;
    }

    /**
     * @Route("team/list", name="team_list")
     * @Template(template="team/list.html.twig")
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'teams' => $this->findAllTeams->__invoke(),
        ];
    }
}
