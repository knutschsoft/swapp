<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200209215126 extends AbstractMigration
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
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(
            'UPDATE `walk` SET `ageRanges` = REPLACE(`ageRanges`, "AppBundle", "App");'
        );
        $this->addSql(
            'UPDATE walk t SET t.ageRanges = \'[]\' WHERE t.ageRanges = CAST(\'null\' AS JSON);'
        );
        $this->addSql(
            'UPDATE `team` SET `ageRanges` = REPLACE(`ageRanges`, "AppBundle", "App");'
        );
        $this->addSql(
            'UPDATE `way_point` SET `ageGroups` = REPLACE(`ageGroups`, "AppBundle", "App");'
        );
    }

    #[\Override]
    public function down(Schema $schema): void
    {
    }
}
