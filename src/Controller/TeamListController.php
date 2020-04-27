<?php
declare(strict_types=1);

namespace App\Controller;

use App\Query\FindAllTeams;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Annotation\Route;

class TeamListController
{
    private FindAllTeams $findAllTeams;

    public function __construct(FindAllTeams $findAllTeams)
    {
        $this->findAllTeams = $findAllTeams;
    }

    /**
     * @Route("team/list", name="team_list")
     *
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
