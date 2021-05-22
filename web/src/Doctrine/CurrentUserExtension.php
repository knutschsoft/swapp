<?php
declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return;
        }
        \assert($user instanceof User);

        $rootAlias = $queryBuilder->getRootAliases()[0];
        if (Team::class === $resourceClass && !$this->security->isGranted('ROLE_ADMIN')) {
            $queryBuilder->andWhere(\sprintf(':currentUser MEMBER OF %s.users', $rootAlias));
            $queryBuilder->setParameter('currentUser', $user->getId());
        }
        if (User::class === $resourceClass && !$this->security->isGranted('ROLE_ADMIN')) {
            $queryBuilder->setParameter('currentUser', $user->getId());
            $andWhere = \sprintf(':currentUser = %s.id', $rootAlias);
            foreach ($user->getTeams() as $key => $team) {
                $andWhere .= ' OR ';
                $andWhere .= \sprintf(':team%s MEMBER OF %s.teams', $key, $rootAlias);
                $queryBuilder->setParameter(\sprintf('team%s', $key), $team);
            }
            $queryBuilder->andWhere($andWhere);
        }
    }
}
