<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211105154638 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ALTER createdAt DROP DEFAULT');
        $this->addSql('ALTER TABLE user ALTER updatedAt DROP DEFAULT');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
    }

    #[\Override]
    public function isTransactional(): bool
    {
        return false;
    }
}
