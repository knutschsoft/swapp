<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230426061306 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD conceptOfDaySuggestions JSON NOT NULL');
        $this->addSql('UPDATE team SET conceptOfDaySuggestions=\'[]\' WHERE id > 0');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP conceptOfDaySuggestions');
    }
}
