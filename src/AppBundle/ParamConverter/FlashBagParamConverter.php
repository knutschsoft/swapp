<?php
namespace AppBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashBagParamConverter implements ParamConverterInterface
{
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
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
        $flashBag = $this->session->getFlashBag();
        $param = $configuration->getName();
        $request->attributes->set($param, $flashBag);

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

        return $configuration->getClass() === 'Symfony\Component\HttpFoundation\Session\Flash\FlashBag';
    }
}
