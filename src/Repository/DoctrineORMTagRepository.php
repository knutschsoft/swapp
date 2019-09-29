<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineORMTagRepository extends ServiceEntityRepository implements TagRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function updateTag(Tag $tag): void
    {
        $this->_em->merge($tag);
        $this->_em->flush();
    }

    public function save(Tag $tag): void
    {
        $this->_em->persist($tag);
        $this->_em->flush();
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        $queryBuilder = $this->createQueryBuilder('tag')->select();
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();

        $tagList = [];

        foreach ($result as $tag) {
            $tagList[] = $tag;
        }

        return $tagList;
    }

    /**
     * @return Tag[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->createQueryBuilder('tag')
            ->select();
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
