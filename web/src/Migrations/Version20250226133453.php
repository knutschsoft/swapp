<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250226133453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithCounselings TINYINT(1) NOT NULL DEFAULT FALSE, ADD isWithMedicals TINYINT(1) NOT NULL DEFAULT FALSE, ADD counselingNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\', ADD medicalNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE walk ADD isWithCounselings TINYINT(1) NOT NULL DEFAULT FALSE, ADD isWithMedicals TINYINT(1) NOT NULL DEFAULT FALSE, ADD counselingNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\', ADD medicalNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE way_point ADD counselings JSON NOT NULL COMMENT \'(DC2Type:json_document)\', ADD medicals JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('UPDATE team SET counselingNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE walk SET counselingNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE way_point SET counselings = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE team SET medicalNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE walk SET medicalNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE way_point SET medicals = \'[]\' WHERE id <> \'\'');
        $this->addSql('ALTER TABLE team CHANGE isWithCounselings isWithCounselings TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE isWithCounselings isWithCounselings TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE team CHANGE isWithMedicals isWithMedicals TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE isWithMedicals isWithMedicals TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP isWithCounselings, DROP isWithMedicals, DROP counselingNames, DROP medicalNames');
        $this->addSql('ALTER TABLE walk DROP isWithCounselings, DROP isWithMedicals, DROP counselingNames, DROP medicalNames');
        $this->addSql('ALTER TABLE way_point DROP counselings, DROP medicals');
    }
}
