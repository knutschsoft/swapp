<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ClientRepository
{
    public function refresh(Client $client): void;

    public function save(Client $client): void;

    public function findOneByName(string $value): Client;

    public function findOneByEmail(string $value): Client;
}
