<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230419084948 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD walkNames JSON NOT NULL');
        $this->addSql('UPDATE team SET walkNames=\'[]\' WHERE id > 0');

    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP walkNames');
    }
}
