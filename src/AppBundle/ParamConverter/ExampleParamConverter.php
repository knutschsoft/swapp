<?php
namespace AppBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class ExampleParamConverter implements ParamConverterInterface
{

    /**
     * Stores an object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool    True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        switch ($configuration->getName()) {
            case 'stdClass':
                $resource = new \stdClass();
                $resource->name = sprintf('I\'m a stdClass with name %s', $request->get('name'));
                break;
            case 'anotherStdClass':
                $resource = new \stdClass();
                $resource->name = sprintf('I\'m another stdClass with name %s', $request->get('anotherName'));
                break;
            default:
                throw new \InvalidArgumentException(
                    sprintf('Wrong configuration "%s" in ', $configuration->getName(), __CLASS__)
                );
        }

        $param = $configuration->getName();
        $request->attributes->set($param, $resource);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool    True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        $resourceNames = array(
            'stdClass',
            'anotherStdClass',
        );

        return (in_array($configuration->getName(), $resourceNames));
            //&& $configuration->getClass() === 'AppBundle\Type\MyKewlClass');
    }
}
