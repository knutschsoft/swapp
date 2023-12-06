<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211015182522 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD locationNames LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('UPDATE team SET locationNames = \'a:0:{}\'');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP locationNames');
    }

    #[\Override]
    public function isTransactional(): bool
    {
        return false;
    }
}
