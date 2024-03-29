<?php
declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Dto\TeamName;
use App\Entity\Client;
use App\Entity\SystemicQuestion;
use App\Entity\Tag;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Walk;
use App\Entity\WayPoint;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private readonly Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass, $operation);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass, $operation);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, ?Operation $operation = null): void
    {
        $user = $this->security->getUser();
        if (null === $user) {
            return;
        }
        \assert($user instanceof User);

        $rootAlias = $queryBuilder->getRootAliases()[0];
        // a normal user only sees teams he is member of
        // an admin only sees all teams of his client
        if (Team::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                $queryBuilder->andWhere(\sprintf(':currentUser MEMBER OF %s.users', $rootAlias));
                $queryBuilder->setParameter('currentUser', $user->getId());
            } elseif (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $queryBuilder->setParameter('client', $user->getClient());
                $andWhere = \sprintf(':client = %s.client', $rootAlias);
                $queryBuilder->andWhere($andWhere);
            }
        }
        // a normal user only sees users of his client
        // an admin only sees users of his client
        if (User::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_ADMIN')) {
                $queryBuilder->setParameter('currentUser', $user->getId());
                $andWhere = \sprintf(':currentUser = %s.id', $rootAlias);
                foreach ($user->getTeams() as $key => $team) {
                    $andWhere .= ' OR ';
                    $andWhere .= \sprintf(':team%s MEMBER OF %s.teams', $key, $rootAlias);
                    $queryBuilder->setParameter(\sprintf('team%s', $key), $team);
                }
                $queryBuilder->andWhere($andWhere);
            }

            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $queryBuilder->andWhere(\sprintf(':client = %s.client', $rootAlias));
                $queryBuilder->setParameter('client', $user->getClient());
            }
        }

        // a normal user only sees walks of his client
        // an admin only sees walks of his client
        if (Walk::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $queryBuilder->andWhere(\sprintf(':client = %s.client', $rootAlias));
                $queryBuilder->setParameter('client', $user->getClient());
            }

            if ($operation?->getOutput() && TeamName::class === $operation->getOutput()['class']) {
                $queryBuilder->select(\sprintf('%s.teamName', $rootAlias));
                $queryBuilder->groupBy(\sprintf('%s.teamName', $rootAlias));
                $queryBuilder->andWhere(\sprintf('LENGTH(%s.teamName) > 1', $rootAlias));
            }
        }

        // a normal user only sees wayPoints of his client
        // an admin only sees payPoints of his client
        if (WayPoint::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $queryBuilder->leftJoin(\sprintf('%s.walk', $rootAlias), 'walk');
                $queryBuilder->andWhere(':client = walk.client');
                $queryBuilder->setParameter('client', $user->getClient());
            }
        }

        // a normal user only sees tags of his client
        // an admin only sees tags of his client
        if (Tag::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $queryBuilder->andWhere(\sprintf(':client = %s.client', $rootAlias));
                $queryBuilder->setParameter('client', $user->getClient());
            }
        }

        // a normal user only sees systemicQuestions of his client
        // an admin only sees systemicQuestions of his client
        if (SystemicQuestion::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $queryBuilder->andWhere(\sprintf(':client = %s.client', $rootAlias));
                $queryBuilder->setParameter('client', $user->getClient());
            }
        }

        // a normal user only sees his own client
        // an admin only sees his own client
        if (Client::class === $resourceClass) {
            if (!$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                $queryBuilder->andWhere(\sprintf(':client = %s', $rootAlias));
                $queryBuilder->setParameter('client', $user->getClient());
            }
        }
    }
}
