<?php
declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250225035824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team ADD isWithConsumables TINYINT(1) NOT NULL DEFAULT FALSE, ADD consumableNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE walk ADD isWithConsumables TINYINT(1) NOT NULL DEFAULT FALSE, ADD consumableNames JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('ALTER TABLE way_point ADD consumables JSON NOT NULL COMMENT \'(DC2Type:json_document)\'');
        $this->addSql('UPDATE team SET consumableNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE walk SET consumableNames = \'[]\' WHERE id <> \'\'');
        $this->addSql('UPDATE way_point SET consumables = \'[]\' WHERE id <> \'\'');
        $this->addSql('ALTER TABLE team CHANGE isWithConsumables isWithConsumables TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE walk CHANGE isWithConsumables isWithConsumables TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE team DROP isWithConsumables, DROP consumableNames');
        $this->addSql('ALTER TABLE walk DROP isWithConsumables, DROP consumableNames');
        $this->addSql('ALTER TABLE way_point DROP consumables');
    }
}
