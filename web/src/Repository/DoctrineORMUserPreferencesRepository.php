<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserPreferences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPreferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPreferences|null findOneBy(string[] $criteria, string[]|null $orderBy = null)
 * @method UserPreferences[]    findAll()
 * @method UserPreferences[]    findBy(string[] $criteria, string[]|null $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<UserPreferences>
 */
class DoctrineORMUserPreferencesRepository extends ServiceEntityRepository implements UserPreferencesRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPreferences::class);
    }

    /**
     * @param mixed $id
     *
     * @return UserPreferences|null
     */
    public function findOneById(mixed $id): ?UserPreferences
    {
        /** @var UserPreferences|null $userPreferences */
        $userPreferences = parent::findOneBy(['id' => $id]);

        return $userPreferences;
    }

    public function save(UserPreferences $userPreferences): void
    {
        $this->getEntityManager()->persist($userPreferences);
        $this->getEntityManager()->flush();
    }

    public function remove(UserPreferences $userPreferences): void
    {
        $this->getEntityManager()->remove($userPreferences);
        $this->getEntityManager()->flush();
    }
}
