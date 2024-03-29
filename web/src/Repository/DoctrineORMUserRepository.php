<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Repository\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<User>
 */
class DoctrineORMUserRepository extends ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function refresh(User $user): void
    {
        $this->_em->refresh($user);
    }

    public function save(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function loadUserByUsername(string $username): ?UserInterface
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :query OR u.email = :query')
            ->setParameter('query', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    public function findByIdAndConfirmationToken(string $userId, string $confirmationToken): User
    {
        $queryBuilder = $this
            ->createQueryBuilder('user')
            ->select('user')
            ->where('user.id = :userId')
            ->andWhere('user.confirmationToken.token = :confirmationToken')
            ->setParameter('userId', $userId)
            ->setParameter('confirmationToken', $confirmationToken);

        return $this->oneOrException($queryBuilder);
    }

    public function findOneByEmailOrUsername(string $emailOrUsername): User
    {
        $queryBuilder = $this
            ->createQueryBuilder('user')
            ->select('user')
            ->where('user.username = :username')
            ->orWhere('user.email = :email')
            ->setParameter('username', $emailOrUsername)
            ->setParameter('email', $emailOrUsername);

        return $this->oneOrException($queryBuilder);
    }

    protected function oneOrException(QueryBuilder $queryBuilder): User
    {
        try {
            $model = $queryBuilder
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            throw new NotFoundException();
        }

        if (null === $model) {
            throw new NotFoundException();
        }
        \assert($model instanceof User);

        return $model;
    }
}
