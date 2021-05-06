<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\WalkRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WalkExportController
{
    private WalkRepository $walkRepository;

    public function __construct(WalkRepository $walkRepository)
    {
        $this->walkRepository = $walkRepository;
    }

    /**
     * @Route("walkexport", name="walk_export")
     *
     * @return Response
     *
     * @throws \League\Csv\CannotInsertRecord
     */
    public function __invoke(): Response
    {

    }
}
