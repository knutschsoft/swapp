<?php
namespace AppBundle\ParamConverter;

use AppBundle\Entity\Team;
use AppBundle\Entity\Walk;
use AppBundle\Entity\WayPoint;
use AppBundle\Repository\TeamRepositoryInterface;
use AppBundle\Repository\WalkRepositoryInterface;
use AppBundle\Repository\WayPointRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityParamConverter implements ParamConverterInterface
{
    private $walkRepository;
    private $teamRepository;
    private $wayPointRepository;

    /**
     * @param TeamRepositoryInterface     $teamRepository
     * @param WalkRepositoryInterface     $walkRepository
     * @param WayPointRepositoryInterface $wayPointRepository
     */
    public function __construct(
        TeamRepositoryInterface $teamRepository,
        WalkRepositoryInterface $walkRepository,
        WayPointRepositoryInterface $wayPointRepository
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
    public function apply(Request $request, ParamConverter $configuration)
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
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Wrong configuration "%s" in ', $configuration->getName(), __CLASS__)
                );
        }

        if (!$resource) {
            throw new NotFoundHttpException(
                sprintf(
                    'No %s not found for id "%s" in %s',
                    $configuration->getName(),
                    $id,
                    self::class
                )
            );
        }

        $param = $configuration->getName();
        $request->attributes->set($param, $resource);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return in_array($configuration->getClass(), [Team::class, Walk::class, WayPoint::class]);
    }
}
