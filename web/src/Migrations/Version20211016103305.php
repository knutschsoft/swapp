<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211016103305 extends AbstractMigration
{
    #[\Override]
    public function getDescription(): string
    {
        return '';
    }

    #[\Override]
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE way_point ADD oneOnOneInterview VARCHAR(4096) NOT NULL DEFAULT \'\'');
        $this->addSql('ALTER TABLE way_point ALTER oneOnOneInterview DROP DEFAULT');
    }

    #[\Override]
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE way_point DROP oneOnOneInterview');
    }

    #[\Override]
    public function isTransactional(): bool
    {
        return false;
    }
}
