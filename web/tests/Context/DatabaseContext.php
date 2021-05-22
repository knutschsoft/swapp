<?php
declare(strict_types=1);

namespace App\Tests\Context;

use Behat\Behat\Context\Context;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\KernelInterface;

final class DatabaseContext implements Context
{
    private Registry $doctrine;
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->setKernel($kernel);
        $this->doctrine = $this->kernel->getContainer()->get('test.service_container')->get('doctrine');
    }

    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function clearRepositories(): void
    {
        // Maybe by using the purger like in the example above? Up to you.
        // It is also a good practice to clear all the repositories. How you collect all of the repositories: leveraging
        // the framework or manually is up to you.
    }

    /**
     * @BeforeScenario
     */
    public function resetSequences(): void
    {
        //$connection = $this->doctrine->getConnection();

        // If you are using auto-increment IDs, you might want to reset them. It is usually better to purge/reset
        // things at the beginning of a test so that in case of a failure, you are not ending up in a broken state.
        // With PostgreSQL:
        // $connection->executeQuery('ALTER SEQUENCE dummy_sequence RESTART');
        // With MySQL:
        // $connection->executeQuery('ALTER TABLE dummy AUTO_INCREMENT = 1');
    }

    /**
     * @BeforeScenario
     */
    public function beginTransaction(): void
    {
        // $this->doctrine->getConnection()->beginTransaction();
    }

    /**
     * @AfterScenario
     */
    public function rollbackTransaction(): void
    {
        // $this->doctrine->getConnection()->rollBack();
    }

    /**
     * @BeforeScenario
     */
    public function purgeDatabase(): void
    {
        $em = $this->getEntityManager();
        $purger = self::createPurger($em);
        $purger->purge();
    }

    private function getEntityManager(string $entityManagerName = 'default'): ObjectManager
    {
        return $this->doctrine->getManager($entityManagerName);
    }

    private static function createPurger(ObjectManager $manager): ORMPurger
    {
        $metaData = $manager->getMetadataFactory()->getAllMetadata();

        $excluded = [];

        foreach ($metaData as $classMetadata) {
            /** @var ClassMetadata $classMetadata */
            if ($classMetadata->isReadOnly) {
                $excluded[] = $classMetadata->getTableName();
            }
        }

        return new ORMPurger($manager, $excluded);
    }
}
