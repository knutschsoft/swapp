<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Behat\Behat\Context\Context;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;

final class DatabaseContext implements Context
{
    private SchemaTool $schemaTool;
    /** @var ClassMetadata[] */
    private array $classMetadatas;
    private ObjectManager $entityManager;

    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        $this->entityManager = $managerRegistry->getManager();
        if (!$this->entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException(
                'Object manager is not instance of class EntityManager. Please check your configuration.'
            );
        }

        $this->schemaTool = new SchemaTool($this->entityManager);
        $this->classMetadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @BeforeScenario
     */
    public function createDatabase(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $params = $connection->getParams();
        if (isset($params['path'])) {
            $fs = new Filesystem();
            if (!$fs->exists($params['path'])) {
                $this->schemaTool->updateSchema($this->classMetadatas, true);
            }
        }
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase(): void
    {
        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();
        $purger = $this->createPurger();
        $isMySQL = $connection->getDriver()->getDatabasePlatform() instanceof AbstractMySQLPlatform;
        if ($isMySQL) {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');
        }
        $purger->purge();
        if ($isMySQL) {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
        }
    }

    private function createPurger(): ORMPurger
    {
        $excluded = [];

        foreach ($this->classMetadatas as $classMetadata) {
            /** @var ClassMetadata $classMetadata */
            if ($classMetadata->isReadOnly) {
                $excluded[] = $classMetadata->getTableName();
            }
        }

        return new ORMPurger($this->entityManager, $excluded);
    }
}
