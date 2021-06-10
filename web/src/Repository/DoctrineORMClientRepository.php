<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Repository\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Client>
 */
class DoctrineORMClientRepository extends ServiceEntityRepository implements ClientRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function refresh(Client $client): void
    {
        $this->_em->refresh($client);
    }

    public function save(Client $client): void
    {
        $this->_em->persist($client);
        $this->_em->flush();
    }

    public function findOneByName(string $name): Client
    {
        $client = $this->findOneBy(['name' => \trim($name)]);
        if (!$client) {
            throw new NotFoundException(\sprintf('Client with name "%s" not found.', $name));
        }

        return $client;
    }

    public function findOneByEmail(string $email): Client
    {
        $client = $this->findOneBy(['email' => \trim($email)]);
        if (!$client) {
            throw new NotFoundException(\sprintf('Client with email "%s" not found.', $email));
        }

        return $client;
    }

    protected function oneOrException(QueryBuilder $queryBuilder): Client
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
        \assert($model instanceof Client);

        return $model;
    }
}
