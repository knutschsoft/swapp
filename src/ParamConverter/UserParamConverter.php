<?php
declare(strict_types=1);

namespace App\ParamConverter;

use App\Entity\User;
use App\Security\UserProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserParamConverter implements ParamConverterInterface
{
    private $tokenStorage;
    /** @var UserProvider */
    private $userProvider;

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserProvider $userProvider)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userProvider = $userProvider;
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
        $user = $this->tokenStorage->getToken()->getUser();
        $param = $configuration->getName();
        $request->attributes->set($param, $user);

        return true;
    }
    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === User::class;
    }
}
