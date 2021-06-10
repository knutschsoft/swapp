<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Tag;
use App\Repository\Exception\NotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Tag>
 */
class DoctrineORMTagRepository extends ServiceEntityRepository implements TagRepository
{
    public function __construct(ManagerRegistry $registry)
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

    /** @return Tag[] */
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

    public function findOneByColorAndNameAndClient(string $color, string $name, Client $client): Tag
    {
        $tag = $this->findOneBy(['color' => $color, 'name' => $name, 'client' => $client]);
        if (!$tag) {
            throw new NotFoundException(
                \sprintf(
                    'Tag with color "%s", name "%s" and client "%s" not found.',
                    $color,
                    $name,
                    $client->getName()
                )
            );
        }

        return $tag;
    }
}
