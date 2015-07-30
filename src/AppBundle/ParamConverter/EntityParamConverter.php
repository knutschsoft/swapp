<?php
namespace AppBundle\ParamConverter;

use AppBundle\Entity\Team;
use AppBundle\Entity\Walk;
use AppBundle\Repository\TeamRepository;
use AppBundle\Repository\WalkRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityParamConverter implements ParamConverterInterface
{
    private $walkRepository;
    private $teamRepository;

    /**
     * @param WalkRepository $walkRepository
     * @param TeamRepository $teamRepository
     */
    public function __construct(WalkRepository $walkRepository, TeamRepository $teamRepository)
    {
        $this->walkRepository = $walkRepository;
        $this->teamRepository = $teamRepository;
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
                $resource = $this->walkRepository->findOneById($request->get('id'));
                break;
            case 'team':
                $resource = $this->teamRepository->findOneById($request->get('team'));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Wrong configuration "%s" in ', $configuration->getName(), __CLASS__)
                );
        }

        if (!$resource) {
            throw new NotFoundHttpException(
                sprintf(
                    'No User not found for id "%s" in %s',
                    $request->get('id'),
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
        return in_array($configuration->getClass(), [Walk::class, Team::class]);
    }
}
