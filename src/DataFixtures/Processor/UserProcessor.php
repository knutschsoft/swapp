<?php
declare(strict_types=1);

namespace App\DataFixtures\Processor;

use App\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Webmozart\Assert\Assert;

class UserProcessor implements ProcessorInterface
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess(string $id, $object): void
    {
        if (false === $object instanceof User) {
            return;
        }
        Assert::isInstanceOf($object, User::class);

        /** @var User $user */
        $user = $object;

        $user->setPassword($this->encoder->encodePassword($user, $user->getPlainPassword()));
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess(string $id, $object): void
    {
        // do nothing
    }
}
