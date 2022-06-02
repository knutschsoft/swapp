<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220519200004 extends AbstractMigration
{
    private array $walks = [];

    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void
    {

        $query = $this->connection->createQueryBuilder()
            ->select(
                'id, startTime'
            )
            ->from('walk')
            ->getSQL();

        $this->walks = $this->connection->executeQuery($query)->fetchAllAssociative();
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE way_point ADD visitedAt DATETIME NOT NULL DEFAULT NOW()');
    }

    public function postUp(Schema $schema): void
    {
        foreach ($this->walks as $walk) {
            $id = $walk['id'];
            $startTime = new \Datetime($walk['startTime']);

            $query = $this->connection->createQueryBuilder()
                ->update('way_point')
                ->set('way_point.visitedAt', "'".$this->connection->convertToDatabaseValue($startTime, 'datetime')."'")
                ->where('way_point.walk_id = '.$this->connection->convertToDatabaseValue($id, 'integer'))
                ->getSQL();

            $this->connection->executeQuery(
                $query
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE way_point DROP visitedAt');
    }
}
