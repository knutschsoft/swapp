<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface UserRepository extends UserLoaderInterface
{
    public function refresh(User $user): void;

    public function save(User $user): void;

    public function findByIdAndConfirmationToken(string $userId, string $confirmationToken): User;

    public function findOneByEmailOrUsername(string $emailOrUsername): User;
}
