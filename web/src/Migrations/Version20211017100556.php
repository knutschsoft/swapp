<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211017100556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD createdAt DATETIME NOT NULL DEFAULT NOW(), ADD updatedAt DATETIME NOT NULL DEFAULT NOW(), ADD createdBy VARCHAR(255) DEFAULT NULL, ADD updatedBy VARCHAR(255) DEFAULT NULL');
        //$this->addSql('ALTER TABLE user ALTER createdAt DROP DEFAULT');
        //$this->addSql('ALTER TABLE user ALTER updatedAt DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP createdAt, DROP updatedAt, DROP createdBy, DROP updatedBy');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
