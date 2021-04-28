<?php
declare(strict_types=1);

namespace App\ParamConverter;

use App\Entity\Team;
use App\Entity\Walk;
use App\Entity\WayPoint;
use App\Repository\TeamRepository;
use App\Repository\WalkRepository;
use App\Repository\WayPointRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityParamConverter implements ParamConverterInterface
{
    private WalkRepository $walkRepository;

    private TeamRepository $teamRepository;

    private WayPointRepository $wayPointRepository;

    public function __construct(
        TeamRepository $teamRepository,
        WalkRepository $walkRepository,
        WayPointRepository $wayPointRepository
    ) {
        $this->teamRepository = $teamRepository;
        $this->walkRepository = $walkRepository;
        $this->wayPointRepository = $wayPointRepository;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        switch ($configuration->getName()) {
            case 'walk':
                $id = $request->get('walkId');
                $resource = $this->walkRepository->findOneById($id);

                break;
            case 'wayPoint':
                $id = $request->get('wayPointId');
                $resource = $this->wayPointRepository->findOneById($id);

                break;
            case 'team':
                $id = $request->get('teamId');
                $resource = $this->teamRepository->findOneById($id);
                if (!$resource) {
                    $id = $request->get('id');
                    $resource = $this->teamRepository->findOneById($id);
                }

                break;
            default:
                throw new \InvalidArgumentException(\sprintf('Wrong configuration "%s" in "%s"', $configuration->getName(), self::class));
        }

        if (!$resource && !$configuration->isOptional()) {
            throw new NotFoundHttpException(\sprintf('No %s not found for id "%s" in %s', $configuration->getName(), $id, self::class));
        }

        $param = $configuration->getName();
        $request->attributes->set($param, $resource);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration): bool
    {
        return \in_array($configuration->getClass(), [Team::class, Walk::class, WayPoint::class]);
    }
}
