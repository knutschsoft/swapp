<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220310065940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team CHANGE isWithContactsCount isWithContactsCount TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk ADD isWithContactsCount TINYINT(1) NOT NULL DEFAULT FALSE');
        $this->addSql('ALTER TABLE walk CHANGE isWithContactsCount isWithContactsCount TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE way_point ADD contactsCount INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team CHANGE isWithContactsCount isWithContactsCount TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE walk DROP isWithContactsCount');
        $this->addSql('ALTER TABLE way_point DROP contactsCount');
    }
}
