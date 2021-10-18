<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211016103305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE way_point ADD oneOnOneInterview VARCHAR(4096) NOT NULL DEFAULT \'\'');
        $this->addSql('ALTER TABLE way_point ALTER oneOnOneInterview DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE way_point DROP oneOnOneInterview');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
