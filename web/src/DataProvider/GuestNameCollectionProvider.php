<?php
declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\GuestName;
use App\Entity\User;
use App\Entity\Walk;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<GuestName> */
class GuestNameCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        Assert::isInstanceOf($user, User::class);

        $walks = $this->em->getRepository(Walk::class)->createQueryBuilder('w')
            ->select('w.guestNames')
            ->where('w.guestNames IS NOT NULL')
            ->andWhere('w.guestNames != \'[]\'')
            ->andWhere('w.client = :client')
            ->setParameter('client', $user->getClient())
            ->getQuery()
            ->getArrayResult();

        /** @var list<string> $allNames */
        $allNames = [];
        foreach ($walks as $entry) {
            Assert::isArray($entry);
            if (empty($entry['guestNames'])) {
                continue;
            }
            Assert::isArray($entry['guestNames']);

            foreach ($entry['guestNames'] as $name) {
                Assert::string($name);
                $allNames[] = \trim($name);
            }
        }

        $uniqueSortedNames = \array_values(\array_unique($allNames));

        \sort($uniqueSortedNames, \SORT_NATURAL | \SORT_FLAG_CASE);

        return \array_map(static fn (string $name) => new GuestName($name), $uniqueSortedNames);
    }
}
